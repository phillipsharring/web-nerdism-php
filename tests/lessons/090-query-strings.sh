# query-strings: https://phillipharrington.com/php/query-strings
serve query-strings
out="$(curl -sg "http://localhost:$PORT/?q=red+shoes&page=2&tag[]=sale&tag[]=new")"
expect "+ decodes to a space" "$out" "Searching for: red shoes"
expect "page is a number" "$out" "Page: 2"
expect "tag[] becomes an array" "$out" "Tags: sale, new"
out="$(curl -sg "http://localhost:$PORT/?q[]=x&page=banana&tag=solo")"
if grep -qE "Searching for: .+" <<< "$out"; then fail "an array where a string belongs is refused" "$out"; else pass "an array where a string belongs is refused"; fi
expect "a word for a page number falls back to 1" "$out" "Page: 1"
expect "a negative page falls back to 1" "$(curl -sg "http://localhost:$PORT/?page=-5")" "Page: 1"
stop
