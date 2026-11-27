# javascript: https://phillipharrington.com/php/javascript
serve javascript -t public
out="$(curl -s "http://localhost:$PORT/")"
expect "the page loads the script, deferred" "$out" '<script src="/app.js" defer></script>'
expect "PHP hands the data over as JSON" "$out" '{"name":"friend","sizes":["Small","Medium","Large"]}'
expect "the script is served as JavaScript" "$(curl -sI "http://localhost:$PORT/app.js")" "javascript"
out="$(curl -s "http://localhost:$PORT/?name=%3C/script%3E%3Cscript%3Ealert(1)%3C/script%3E")"
expect "a closing script tag in the data comes out as \\u codes" "$out" '{"name":"\u003C'
expect "  ...and as text in the heading" "$out" "Hello, &lt;/script&gt;&lt;script&gt;alert(1)"
if grep -qF "<script>alert" <<< "$out"; then fail "  ...never as a tag" "$out"; else pass "  ...never as a tag"; fi
stop
expect "app.js is valid JavaScript" "$(docker run --rm -v "$ROOT/javascript/public":/app node:22-alpine node --check /app/app.js 2>&1 && echo valid)" "valid"
