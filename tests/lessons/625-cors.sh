# cors: https://phillipharrington.com/php/cors
serve cors -t api
out="$(curl -si -X OPTIONS -H 'Origin: http://localhost:8001' -H 'Access-Control-Request-Method: POST' -H 'Access-Control-Request-Headers: content-type' "http://localhost:$PORT/")"
expect "a preflight from an allowed origin is a 204" "$out" "HTTP/1.1 204"
expect "  ...naming that origin" "$out" "Access-Control-Allow-Origin: http://localhost:8001"
expect "  ...and the header it may send" "$out" "Access-Control-Allow-Headers: Content-Type"
out="$(curl -si -H 'Origin: http://localhost:8001' -H 'Content-Type: application/json' -d '{}' "http://localhost:$PORT/")"
expect "the real request is answered" "$out" '{"message":"Hello from the API."}'
expect "  ...with permission to read it" "$out" "Access-Control-Allow-Origin: http://localhost:8001"
expect "  ...and Vary: Origin" "$out" "Vary: Origin"
out="$(curl -si -H 'Origin: http://evil.example' "http://localhost:$PORT/")"
if grep -qi "Access-Control-Allow-Origin" <<< "$out"; then fail "another origin gets no permission" "$out"; else pass "another origin gets no permission"; fi
expect "  ...but the server still answers: CORS only binds browsers" "$out" "Hello from the API."
out="$(curl -si -H 'Origin: http://127.0.0.1:8001' "http://localhost:$PORT/")"
if grep -qi "Access-Control-Allow-Origin" <<< "$out"; then fail "127.0.0.1 is not localhost" "$out"; else pass "127.0.0.1 is not localhost"; fi
stop
serve cors -t site
expect "the other site's page" "$(curl -s "http://localhost:$PORT/")" '<script src="/app.js" defer></script>'
stop
expect "app.js is valid JavaScript" "$(docker run --rm -v "$ROOT/cors/site":/app node:22-alpine node --check /app/app.js 2>&1 && echo valid)" "valid"
