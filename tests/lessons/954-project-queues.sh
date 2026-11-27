# project-queues: https://phillipharrington.com/php/projects/queues
q() { APP_PORT=$PORT MAIL_PORT=$((PORT + 1)) docker compose -p "$NAME-queues" -f "$ROOT/project-queues/compose.yml" "$@"; }
sql() { q exec -T db mysql -uapp -papp-password -N course -e "$1" 2>/dev/null; }
mail_count() { curl -s "http://localhost:$((PORT + 1))/api/v1/messages" | sed -n 's/^{"total":\([0-9]*\).*/\1/p'; }
docker run --rm -v "$ROOT/project-queues":/app -w /app composer:2 composer install -q --no-interaction 2>&1
q up -d --build --wait db mailpit app >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
U="http://localhost:$PORT/"

expect "signing up answers at once" "$(curl -si -d email=ann@example.com "$U")" "Location: /?thanks=1"
expect "  ...and queues a job" "$(sql "SELECT status, type, payload FROM jobs")" 'queued	welcome-email	{"email": "ann@example.com"}'
expect "  ...but sends nothing yet" "$(mail_count)" "0"
expect "a bad address is refused, not queued" "$(curl -s -d 'email=not an address' "$U")" "doesn&#039;t look like an email address"
expect "  ...so the queue still has one job" "$(sql "SELECT COUNT(*) FROM jobs")" "1"
expect "the worker runs the job" "$(q run --rm -T app php bin/worker.php --until-empty 2>/dev/null)" "Job 1 (welcome-email) done."
expect "  ...the email arrives" "$(curl -s "http://localhost:$((PORT + 1))/api/v1/messages")" '"To":[{"Name":"","Address":"ann@example.com"}]'
expect "  ...and the job is done" "$(sql "SELECT status, attempts FROM jobs WHERE id = 1")" "done	1"

# The mail server goes down.
q stop mailpit >/dev/null 2>&1
curl -s -o /dev/null -d email=bo@example.com "$U"
out="$(q run --rm -T -e QUEUE_BACKOFF=30 app php bin/worker.php --until-empty 2>/dev/null)"
expect "with the mail server down, the job fails" "$out" "Job 2 (welcome-email) failed, attempt 1"
expect "  ...and goes back in the queue" "$out" "Now queued."
expect "  ...not to be tried again for a while" "$(sql "SELECT status, attempts, available_at > NOW() FROM jobs WHERE id = 2")" "queued	1	1"
expect "  ...with the reason kept" "$(sql "SELECT last_error IS NOT NULL FROM jobs WHERE id = 2")" "1"
sql "UPDATE jobs SET available_at = NOW() WHERE id = 2"
out="$(q run --rm -T -e QUEUE_BACKOFF=0 app php bin/worker.php --until-empty 2>/dev/null)"
expect "after three attempts it gives up" "$out" "attempt 3"
expect "  ...and marks it failed" "$(sql "SELECT status, attempts FROM jobs WHERE id = 2")" "failed	3"
q start mailpit >/dev/null 2>&1
sleep 1

# A job nobody can do: no handler. It fails without stopping the worker.
sql "INSERT INTO jobs (type, payload) VALUES ('mystery', '{}'), ('welcome-email', '{\"email\": \"cy@example.com\"}')"
out="$(q run --rm -T -e QUEUE_BACKOFF=0 app php bin/worker.php --until-empty 2>/dev/null)"
expect "a job with no handler fails" "$out" "No handler for mystery"
expect "  ...and the next job still runs" "$out" "(welcome-email) done."
sql "INSERT INTO jobs (type, payload) VALUES ('welcome-email', '{\"email\": \"<script>@x\"}')"
expect "a bad payload fails in the handler too" "$(q run --rm -T -e QUEUE_BACKOFF=0 app php bin/worker.php --until-empty 2>/dev/null)" "No valid address in the payload."

# Two workers, twenty jobs: each job runs exactly once.
curl -s -X DELETE "http://localhost:$((PORT + 1))/api/v1/messages" >/dev/null
sql "INSERT INTO jobs (type, payload) WITH RECURSIVE n (i) AS (SELECT 1 UNION ALL SELECT i + 1 FROM n WHERE i < 20) SELECT 'welcome-email', JSON_OBJECT('email', CONCAT('user', i, '@example.com')) FROM n"
q run --rm -T app php bin/worker.php --until-empty >/dev/null 2>&1 &
q run --rm -T app php bin/worker.php --until-empty >/dev/null 2>&1
wait
sleep 1
expect "two workers at once: twenty emails, no doubles" "$(mail_count)" "20"
expect "  ...and every job done once" "$(sql "SELECT COUNT(*), MAX(attempts) FROM jobs WHERE payload->>'$.email' LIKE 'user%'")" "20	1"

# A stuck job: a worker died holding it. After ten minutes, it's taken back.
sql "INSERT INTO jobs (type, payload, status, attempts, reserved_at) VALUES ('welcome-email', '{\"email\": \"dee@example.com\"}', 'running', 1, NOW() - INTERVAL 11 MINUTE)"
expect "a job left running too long is taken back" "$(q run --rm -T app php bin/worker.php --until-empty 2>/dev/null)" "done."

# The long-running worker service.
q up -d worker >/dev/null 2>&1
curl -s -o /dev/null -d email=eve@example.com "$U"
for _ in $(seq 1 20); do sql "SELECT status FROM jobs ORDER BY id DESC LIMIT 1" | grep -q done && break; sleep 0.5; done
expect "the worker service picks up new jobs by itself" "$(curl -s "http://localhost:$((PORT + 1))/api/v1/messages")" "eve@example.com"
q down -v >/dev/null 2>&1
