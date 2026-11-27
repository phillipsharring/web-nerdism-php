# project-api-client: https://phillipharrington.com/php/projects/api-client
TOKEN=wnp_live_7d3f9a2c8e1b4f6a
ac() { API_PORT=$PORT API_TOKEN=$TOKEN docker compose -p "$NAME-client" -f "$ROOT/project-api-client/compose.yml" "$@"; }
run() { ac run --rm -T client "$@" 2>&1 | grep -v -E '^ ?Container |^ ?Network '; }
ac up -d api >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
expect "the API wants a token" "$(curl -si "http://localhost:$PORT/tasks")" "HTTP/1.1 401"
out="$(run php bin/tasks.php)"
expect "the client lists the tasks" "$out" "[x] 1 Write the API"
expect "  ...all of them" "$out" "[ ] 2 Write the client"
expect "without a token, it says what's missing" "$(ac run --rm -T -e API_TOKEN= client php bin/tasks.php 2>&1)" "Set API_URL and API_TOKEN"
expect "a wrong token: the API's 401, reported" "$(ac run --rm -T -e API_TOKEN=wrong client php bin/tasks.php 2>&1)" "The API answered 401 for /tasks."
expect "  ...without printing the token" "$(ac run --rm -T -e API_TOKEN=wrong-and-secret client php bin/tasks.php 2>&1 | grep -c 'wrong-and-secret')" "0"
expect "a busy API: retried until it answers" "$(run php bin/tasks.php /flaky)" '{"ok":true}'
start=$(date +%s); out="$(run php bin/tasks.php /slow)"; took=$(( $(date +%s) - start ))
expect "a slow API: given up on" "$out" "Couldn't reach the API for /slow"
if [ "$took" -lt 9 ]; then pass "  ...after the timeout, not the API's 10 seconds (${took}s)"; else fail "  ...after the timeout" "${took}s"; fi
expect "an answer that isn't JSON is refused" "$(run php bin/tasks.php /broken)" "wasn't JSON"
expect "a 404 is reported as one" "$(run php bin/tasks.php /nope)" "The API answered 404"
out="$(ac run --rm -T client php -r 'require "src/ApiError.php"; require "src/ApiClient.php"; try { new ApiClient("http://example.com", "super-secret-token"); } catch (InvalidArgumentException $e) { echo $e->getMessage(), "\n", $e->getTraceAsString(); }' 2>&1)"
expect "it won't send a token over plain HTTP to another host" "$out" "Refusing to send a token to http://example.com: use https://."
expect "  ...and the stack trace hides the token" "$out" "Object(SensitiveParameterValue)"
if grep -qF "super-secret-token" <<< "$out"; then fail "  ...completely" "$out"; else pass "  ...completely"; fi
# Fix It: the token is only ever in a header, so it isn't in the API's log.
if ac logs api 2>&1 | grep -qF "$TOKEN"; then fail "the good client's token never appears in the API's log" "$(ac logs api 2>&1 | tail -5)"; else pass "the good client's token never appears in the API's log"; fi
if grep -rqF "$TOKEN" "$ROOT/project-api-client/src" "$ROOT/project-api-client/bin/tasks.php"; then fail "  ...or in the good client's code" ""; else pass "  ...or in the good client's code"; fi
# Break It: the naive client
expect "naive: it works" "$(run php bin/naive.php)" "Write the API"
expect "naive: and the token is in the code, for anyone with the repository" "$(grep -rn "$TOKEN" "$ROOT/project-api-client/bin")" "bin/naive.php"
expect "naive: and in the API's log, for anyone who can read it" "$(ac logs api 2>&1)" "GET /tasks?api_key=$TOKEN"
ac down >/dev/null 2>&1
