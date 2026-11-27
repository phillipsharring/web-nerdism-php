# crud: https://phillipharrington.com/php/crud
crud() { PHP_PORT=$PORT docker compose -p "$NAME-crud" -f "$ROOT/crud/compose.yml" "$@"; }
crud up -d --build >/dev/null 2>&1
for _ in $(seq 1 120); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 1; done
B="http://localhost:$PORT"
code() { curl -s -o /dev/null -w '%{http_code}' "$@"; }
out="$(curl -s "$B/")"
expect "read: the notes from init.sql" "$out" "Order more oat milk"
expect "  ...in UTF-8" "$out" "Fix the café sign"
out="$(curl -si --data-urlencode 'body=Café au lait <b>' "$B/notes")"
expect "create: redirects" "$out" "303 See Other"
expect "  ...and it's in the list, escaped" "$(curl -s "$B/")" "Café au lait &lt;b&gt;"
expect "  ...an empty note is a 422" "$(code -d 'body=' "$B/notes")" "422"
expect "  ...an array is not a note" "$(code -g -d 'body[]=x' "$B/notes")" "422"
expect "  ...nor is 501 characters" "$(code --data-urlencode "body=$(printf 'x%.0s' $(seq 1 501))" "$B/notes")" "422"
expect "read one: the edit form" "$(curl -s "$B/notes/1/edit")" 'value="Order more oat milk"'
expect "update: redirects" "$(code -d 'body=Order oat milk today' "$B/notes/1")" "303"
expect "  ...and the note changed" "$(curl -s "$B/")" "Order oat milk today"
expect "delete: redirects" "$(code -X POST "$B/notes/2/delete")" "303"
if grep -qF "café sign" <<< "$(curl -s "$B/")"; then fail "  ...and the note is gone" "$(curl -s "$B/")"; else pass "  ...and the note is gone"; fi
expect "a note that isn't there is a 404" "$(code "$B/notes/2/edit")" "404"
expect "  ...to update" "$(code -d 'body=x' "$B/notes/2")" "404"
expect "a method we don't take is a 405" "$(code -X DELETE "$B/notes/1")" "405"
crud down -v >/dev/null 2>&1
