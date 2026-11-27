# how-php-runs: https://phillipharrington.com/php/how-php-runs
fpm() { WEB_PORT=$PORT docker compose -p "$NAME-fpm" -f "$ROOT/how-php-runs/compose.yml" "$@"; }
fpm up -d >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/style.css" && break; sleep 0.5; done
for _ in $(seq 1 30); do curl -s "http://localhost:$PORT/" | grep -q fpm-fcgi && break; sleep 0.5; done
out="$(curl -s "http://localhost:$PORT/")"
expect "PHP runs under FPM" "$out" "PHP is running as: fpm-fcgi"
expect "  ...with the script nginx named" "$out" "Script:            /app/public/index.php"
expect "  ...behind nginx" "$out" "Web server:        nginx/"
expect "a PHP answer says PHP made it" "$(curl -sI "http://localhost:$PORT/")" "X-Powered-By: PHP/"
out="$(curl -sI "http://localhost:$PORT/style.css")"
expect "nginx sends the stylesheet" "$out" "Content-Type: text/css"
if grep -qi "X-Powered-By" <<< "$out"; then fail "  ...without PHP" "$out"; else pass "  ...without PHP"; fi
expect "an unknown path goes to index.php" "$(curl -s "http://localhost:$PORT/anything")" "fpm-fcgi"
expect "a .php file that isn't there is a 404" "$(curl -s -o /dev/null -w '%{http_code}' "http://localhost:$PORT/nope.php")" "404"
pids="$(for _ in 1 2 3 4 5 6 7 8; do curl -s "http://localhost:$PORT/" | grep Worker; done | sort -u | wc -l | tr -d " ")"
if [ "$pids" -ge 1 ] && [ "$pids" -le 5 ]; then pass "a small pool of workers takes turns ($pids seen)"; else fail "a small pool of workers takes turns" "$pids"; fi
expect "the pool is a master and its workers" "$(fpm top php 2>&1)" "php-fpm: pool www"
fpm down -v >/dev/null 2>&1
