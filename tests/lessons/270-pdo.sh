# pdo: https://phillipharrington.com/php/pdo
lib() { PHP_PORT=$PORT docker compose -p "$NAME-pdo" -f "$ROOT/pdo/compose.yml" "$@"; }
lib up -d --build >/dev/null 2>&1
for _ in $(seq 1 120); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 1; done
out="$(curl -s "http://localhost:$PORT/")"
expect "all four books from init.sql" "$out" "4 books"
expect "  ...UTF-8 intact on the way out" "$out" "Les Misérables, by Victor Hugo"
expect "a search, with a prepared statement" "$(curl -s "http://localhost:$PORT/?q=shelley")" "1 book"
out="$(curl -s --get --data-urlencode "q=' OR '1'='1" "http://localhost:$PORT/")"
expect "an injection attempt is just a search that finds nothing" "$out" "0 books"
expect "  ...and comes back escaped" "$out" "value=\"&#039; OR &#039;1&#039;=&#039;1\""
expect "% is a real percent sign, not a wildcard" "$(curl -s --get --data-urlencode "q=%" "http://localhost:$PORT/")" "0 books"
out="$(curl -si --data-urlencode 'title=Café Stories' --data-urlencode "author=O'Brien" "http://localhost:$PORT/")"
expect "adding a book redirects" "$out" "303 See Other"
expect "  ...with its new id" "$out" "Location: /?added=5"
out="$(curl -s --get --data-urlencode "q=O'Brien" "http://localhost:$PORT/")"
expect "  ...and it's there, quote and accent intact" "$out" "Café Stories, by O&#039;Brien"
out="$(curl -s --data-urlencode 'title=<script>alert(1)</script>' -d 'author=Mallory' "http://localhost:$PORT/")"
out="$(curl -s "http://localhost:$PORT/?q=Mallory")"
expect "HTML stored in the database comes back escaped" "$out" "&lt;script&gt;alert(1)&lt;/script&gt;"
if grep -qF "<script>alert" <<< "$out"; then fail "  ...never raw" "$out"; else pass "  ...never raw"; fi
expect "an empty title is refused" "$(curl -s -d 'title=&author=Someone' "http://localhost:$PORT/")" "A title, please"
expect "an array instead of a title is refused" "$(curl -s -g -d 'title[]=x&author=Someone' "http://localhost:$PORT/")" "A title, please"
lib down -v >/dev/null 2>&1
