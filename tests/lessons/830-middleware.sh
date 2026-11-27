# middleware: https://phillipharrington.com/php/middleware
docker run --rm -v "$ROOT/middleware":/app -w /app composer:2 composer install -q 2>&1
serve middleware -t public public/index.php
B="http://localhost:$PORT"
out="$(curl -si "$B/notes")"
expect "no token: stopped at RequireToken with a 401" "$out" "401 Unauthorized"
expect "  ...and the handler never ran" "$out" '{"error":"Log in first."}'
expect "  ...but AddRequestId, outside it, still stamped it" "$out" "X-Request-Id: "
out="$(curl -si -H 'Authorization: Bearer alice-token' "$B/notes")"
expect "a good token reaches the handler" "$out" '{"notes":["Order more oat milk","Fix the café sign"]}'
expect "  ...and gets a request id on the way out" "$out" "X-Request-Id: "
expect "a forged token is a 401" "$(curl -s -o /dev/null -w '%{http_code}' -H 'Authorization: Bearer admin' "$B/notes")" "401"
out="$(curl -si -H 'Authorization: Bearer alice-token' "$B/boom")"
expect "an exception becomes a plain 500" "$out" '{"error":"Something went wrong."}'
if grep -qF "fire" <<< "$out"; then fail "  ...with no details for the client" "$out"; else pass "  ...with no details for the client"; fi
if grep -qF "X-Request-Id" <<< "$out"; then fail "  ...and no request id: the exception skipped that layer" "$out"; else pass "  ...and no request id: the exception skipped that layer"; fi
expect "  ...the details went to the log" "$(docker logs "$NAME" 2>&1)" "GET /boom: the database is on fire"
stop
