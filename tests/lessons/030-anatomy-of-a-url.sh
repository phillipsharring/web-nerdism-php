# anatomy-of-a-url: https://phillipharrington.com/php/anatomy-of-a-url
out="$(cli anatomy-of-a-url url.php)"
expect "parse_url finds every part" "$out" "[port] => 8080"
expect "  ...including the fragment" "$out" "[fragment] => comments"
serve anatomy-of-a-url
out="$(curl -s "http://localhost:$PORT/anything/you/like?name=you&page=2#here")"
expect "the server gets the path and query" "$out" "Path and query: /anything/you/like?name=you&page=2"
expect "  ...and \$_GET is decoded" "$out" "[page] => 2"
if grep -qF "#here" <<< "$out"; then fail "the fragment never arrives" "$out"; else pass "the fragment never arrives"; fi
stop
