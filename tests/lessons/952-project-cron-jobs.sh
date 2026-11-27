# project-cron-jobs: https://phillipharrington.com/php/projects/cron-jobs
cj() { docker compose -p "$NAME-cron" -f "$ROOT/project-cron-jobs/compose.yml" "$@"; }
count() { cj exec -T db mysql -uapp -papp-password -N course -e 'SELECT COUNT(*) FROM login_attempts' 2>/dev/null; }
rm -f "$ROOT/project-cron-jobs/var/prune.log"
cj up -d --wait db >/dev/null 2>&1
cj build -q app >/dev/null 2>&1
expect "the table starts with 2,505 rows" "$(count)" "2505"
expect "a dry run counts" "$(cj run --rm -T app php bin/prune.php --dry-run 2>&1)" "Dry run: would prune 2500 rows older than 30 days."
expect "  ...and deletes nothing" "$(count)" "2505"
out="$(cj run --rm -T app php bin/prune.php 2>&1)"
expect "a real run prunes the old ones, in batches" "$out" "Pruned 2500 rows older than 30 days."
expect "  ...with a UTC time on the line" "$out" "Z Pruned"
expect "  ...and keeps the recent ones" "$(count)" "5"
expect "running it again finds nothing to do" "$(cj run --rm -T app php bin/prune.php 2>&1)" "Pruned 0 rows"
expect "a bad --days: exit 2" "$(cj run --rm -T app sh -c 'php bin/prune.php --days=soon; echo "exit $?"' 2>&1)" "exit 2"
expect "while another run holds the lock, it steps aside" "$(cj run --rm -T app sh -c 'flock /tmp/prune.lock sleep 3 & sleep 1; php bin/prune.php' 2>&1)" "Already running; skipped."
expect "a database it can't reach: exit 1" "$(cj run --rm -T -e DB_HOST=nowhere app sh -c 'php bin/prune.php; echo "exit $?"' 2>&1)" "exit 1"
expect "  ...with the reason on STDERR" "$(cj run --rm -T -e DB_HOST=nowhere app php bin/prune.php 2>&1 >/dev/null)" "Failed:"
# Cron itself: start the daemon and wait for its first run (up to about 70 seconds).
cj --profile cron up -d --build cron >/dev/null 2>&1
for _ in $(seq 1 75); do grep -q "Pruned" "$ROOT/project-cron-jobs/var/prune.log" 2>/dev/null && break; sleep 1; done
expect "cron runs it on schedule, with the settings from the crontab" "$(cat "$ROOT/project-cron-jobs/var/prune.log" 2>&1)" "Pruned 0 rows older than 30 days."
cj --profile cron --profile manual down -v >/dev/null 2>&1
rm -f "$ROOT/project-cron-jobs/var/prune.log"
