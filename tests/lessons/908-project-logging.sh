# project-logging: https://phillipharrington.com/php/projects/logging
docker run --rm -v "$ROOT/project-logging":/app -w /app composer:2 composer install --no-interaction --quiet 2>&1
rm -rf "$ROOT/project-logging/logs"
serve project-logging -t public
log="$ROOT/project-logging/logs/app.log"
out="$(curl -si "http://localhost:$PORT/")"
expect "the page answers with a request id" "$out" "X-Request-Id: "
id="$(grep -i '^X-Request-Id:' <<< "$out" | awk '{print $2}' | tr -d '\r')"
expect "  ...and the same id is in its log line" "$(cat "$log")" "app.INFO: Home page viewed [] {\"request_id\":\"$id\"}"
curl -s "http://localhost:$PORT/search?q=caf%C3%A9" >/dev/null
expect "input goes in the context, as UTF-8" "$(cat "$log")" 'app.INFO: Search {"q":"café"}'
curl -s "http://localhost:$PORT/search" >/dev/null
expect "an empty search is a warning" "$(cat "$log")" "app.WARNING: Search with no query"
out="$(curl -si "http://localhost:$PORT/boom")"
expect "an uncaught exception is a 500" "$out" "HTTP/1.1 500"
expect "  ...the visitor sees only an apology" "$out" "Something went wrong."
if grep -qF "RuntimeException" <<< "$out"; then fail "  ...and no details" "$out"; else pass "  ...and no details"; fi
expect "  ...while the log has the details" "$(cat "$log")" "app.ERROR: Uncaught exception {\"exception\":\"[object] (RuntimeException(code: 0): The /boom page always fails, on purpose."
curl -s "http://localhost:$PORT/search?q=x%0A%5B2026-01-01T00:00:00%5D%20app.CRITICAL:%20forged" >/dev/null
if grep -q '^\[2026-01-01' "$log"; then fail "a newline in input can't forge a log line" "$(cat "$log")"; else pass "a newline in input can't forge a log line"; fi
expect "  ...it's written as \\n instead" "$(cat "$log")" '{"q":"x\n[2026-01-01'
stop
rm -rf "$ROOT/project-logging/logs"
