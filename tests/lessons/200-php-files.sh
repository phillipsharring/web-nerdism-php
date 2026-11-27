# php-files: https://phillipharrington.com/php/php-files
docker rm -f "$NAME" >/dev/null 2>&1
docker run -d --name "$NAME" -p "$PORT:8000" -e API_KEY=pretend-secret -v "$ROOT/php-files":/app -w /app "$IMAGE" \
    php -S 0.0.0.0:8000 >/dev/null
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
out="$(curl -s "http://localhost:$PORT/")"
expect "the HTML outside the tags comes through" "$out" "This line is plain HTML."
expect "  ...and what PHP printed" "$out" "Hello, world."
if grep -qE "<\?(php|=)|\\\$name" <<< "$out"; then fail "  ...but never the PHP itself" "$out"; else pass "  ...but never the PHP itself"; fi
if grep -qE "Good morning|Good afternoon" <<< "$out"; then pass "  ...and only one branch of the if"; else fail "  ...and only one branch of the if" "$out"; fi
out="$(curl -s "http://localhost:$PORT/info.php")"
expect "phpinfo() shows the version" "$out" "PHP Version 8.4"
expect "  ...and the environment, secrets included" "$out" "pretend-secret"
stop
expect "php --ini: no php.ini in the image" "$(docker run --rm "$IMAGE" php --ini 2>&1)" "Loaded Configuration File:         (none)"
