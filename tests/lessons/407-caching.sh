# caching: https://phillipharrington.com/php/caching
serve caching -t public
# serve itself asked for / to see the server was up; start from no copy
cli caching -r 'array_map("unlink", glob("cache/*.html"));' >/dev/null
U="http://localhost:$PORT/"
out="$(curl -si -w 'took=%{time_total}' "$U")"
expect "the first request is a miss" "$out" "X-Cache: MISS"
expect "  ...and builds the page" "$out" "<h1>Store Hours</h1>"
slow="$(sed -n 's/.*took=//p' <<< "$out")"
if awk "BEGIN{exit !($slow >= 2)}"; then pass "  ...slowly ($slow s)"; else fail "  ...slowly" "$slow"; fi
first_built="$(grep -o 'Built at [0-9:]*' <<< "$out")"
out="$(curl -si -w 'took=%{time_total}' "$U")"
expect "the second is a hit" "$out" "X-Cache: HIT"
fast="$(sed -n 's/.*took=//p' <<< "$out")"
if awk "BEGIN{exit !($fast < 0.5)}"; then pass "  ...and fast ($fast s)"; else fail "  ...and fast" "$fast"; fi
expect "  ...with the same page" "$out" "$first_built"
expect "anyone may keep it a minute" "$out" "Cache-Control: public, max-age=60"
etag="$(sed -n 's/^ETag: //p' <<< "$out" | tr -d '\r')"
expect "  ...and it has an ETag" "$etag" '"'
out="$(curl -si -H "If-None-Match: $etag" "$U")"
expect "the matching ETag gets a 304" "$out" "HTTP/1.1 304"
if grep -qF 'Store Hours' <<< "$out"; then fail "  ...with no body" "$out"; else pass "  ...with no body"; fi
expect "a made-up ETag gets the whole page" "$(curl -si -H 'If-None-Match: "made-up"' "$U")" "HTTP/1.1 200"
cli caching -r 'array_map("unlink", glob("cache/*.html"));' >/dev/null
sleep 1
out="$(curl -si "$U")"
expect "after the copy is thrown away, a miss" "$out" "X-Cache: MISS"
new_etag="$(sed -n 's/^ETag: //p' <<< "$out" | tr -d '\r')"
if [ -n "$new_etag" ] && [ "$new_etag" != "$etag" ]; then pass "  ...with a new ETag"; else fail "  ...with a new ETag" "$new_etag"; fi
expect "  ...so the old ETag gets the whole page" "$(curl -si -H "If-None-Match: $etag" "$U")" "HTTP/1.1 200"
out="$(curl -si "http://localhost:$PORT/hello.php?name=Ada")"
expect "one person's page is private, no-store" "$out" "Cache-Control: private, no-store"
expect "  ...and says hello" "$out" "Hello, Ada."
expect "input in it is escaped" "$(curl -s --get --data-urlencode 'name=<b>Ada</b>' "http://localhost:$PORT/hello.php")" "Hello, &lt;b&gt;Ada&lt;/b&gt;."
expect "no name, a stranger" "$(curl -s "http://localhost:$PORT/hello.php")" "Hello, stranger."
stop
cli caching -r 'array_map("unlink", glob("cache/*.html"));' >/dev/null
