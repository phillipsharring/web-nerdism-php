# fetch: https://phillipharrington.com/php/fetch
serve fetch -t public
expect "the page has the search box" "$(curl -s "http://localhost:$PORT/")" '<input id="q" autocomplete="off">'
out="$(curl -si "http://localhost:$PORT/search.php?q=b")"
expect "the search answers in JSON" "$out" "Content-Type: application/json"
expect "  ...with the matches" "$out" '{"query":"b","matches":["Black","Blue","Brown"]}'
expect "no search, no matches" "$(curl -s "http://localhost:$PORT/search.php")" '"matches":[]'
long="$(printf 'x%.0s' $(seq 1 51))"
out="$(curl -si "http://localhost:$PORT/search.php?q=$long")"
expect "too long is a 400" "$out" "HTTP/1.1 400"
expect "  ...with a reason, in JSON" "$out" '{"error":"Search for 50 characters or fewer."}'
expect "an array instead of a string is a 400" "$(curl -s -g -o /dev/null -w '%{http_code}' "http://localhost:$PORT/search.php?q[]=b")" "400"
stop
expect "search.js is valid JavaScript" "$(docker run --rm -v "$ROOT/fetch/public":/app node:22-alpine node --check /app/search.js 2>&1 && echo valid)" "valid"
