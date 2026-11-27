# whats-in-the-middle: https://phillipharrington.com/php/whats-in-the-middle
mid() { PHP_PORT=$((PORT - 1)) PROXY_PORT=$PORT docker compose -p "$NAME-middle" -f "$ROOT/whats-in-the-middle/compose.yml" "$@"; }
mid up -d >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
expect "directly, no X-Forwarded-For" "$(curl -s "http://localhost:$((PORT - 1))/")" "X-Forwarded-For:        (none)"
out="$(curl -s "http://localhost:$PORT/")"
if grep -qF "(none)" <<< "$out"; then fail "through the proxy, X-Forwarded-For is set" "$out"; else pass "through the proxy, X-Forwarded-For is set"; fi
expect "the Host comes through unchanged" "$out" "Host:                   localhost:$PORT"
expect "the proxy marks the answer" "$(curl -sI "http://localhost:$PORT/")" "X-Passed-Through: proxy"
expect "a forged address arrives first, the real one last" "$(curl -s -H 'X-Forwarded-For: 1.2.3.4' "http://localhost:$PORT/")" "X-Forwarded-For:        1.2.3.4, "
mid down >/dev/null 2>&1
