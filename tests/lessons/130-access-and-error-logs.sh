# access-and-error-logs: https://phillipharrington.com/php/access-and-error-logs
logs() { PROXY_PORT=$PORT docker compose -p "$NAME-logs" -f "$ROOT/access-and-error-logs/compose.yml" "$@"; }
dir="$ROOT/access-and-error-logs/logs"
logs up -d >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
mark="test-$RANDOM$RANDOM"
expect "the page answers" "$(curl -s "http://localhost:$PORT/")" "Hello. Now go and read the logs."
curl -s -o /dev/null -A "$mark-agent" "http://localhost:$PORT/$mark"
sleep 1
expect "the access log records the 404" "$(cat "$dir/access.log")" "\"GET /$mark HTTP/1.1\" 404"
expect "  ...with whatever user agent the client sent" "$(cat "$dir/access.log")" "\"$mark-agent\""
out="$(curl -s "http://localhost:$PORT/broken")"
expect "the broken page still answers" "$out" "Hello, ."
if grep -qF "Warning" <<< "$out"; then fail "  ...without showing the visitor the error" "$out"; else pass "  ...without showing the visitor the error"; fi
sleep 1
expect "  ...and the warning is in the PHP error log" "$(tail -5 "$dir/php-error.log")" 'PHP Warning:  Undefined variable $name in /app/public/index.php'
logs stop php >/dev/null 2>&1
code="$(curl -s -o /dev/null -w '%{http_code}' "http://localhost:$PORT/$mark-down")"
case "$code" in 502|504) pass "with PHP stopped, nginx answers $code" ;; *) fail "with PHP stopped, nginx answers 502 or 504" "$code" ;; esac
expect "  ...and says why in its error log" "$(cat "$dir/error.log")" "upstream, client: "
expect "  ...for that request" "$(grep "upstream, client" "$dir/error.log")" "GET /$mark-down"
logs down -v >/dev/null 2>&1
