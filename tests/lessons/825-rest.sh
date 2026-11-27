# rest: https://phillipharrington.com/php/rest
serve rest -t public public/index.php
B="http://localhost:$PORT"
J='Content-Type: application/json'
bodycode() { curl -s -w " %{http_code}" "$@" | tr -d "\n"; }
expect "GET the collection" "$(bodycode "$B/notes")" '{"id":2,"body":"Fix the café sign"}]} 200'
out="$(curl -si -H "$J" -d '{"body":"Buy beans ☕"}' "$B/notes")"
expect "POST creates: 201" "$out" "HTTP/1.1 201 Created"
expect "  ...with a Location" "$out" "Location: /notes/3"
expect "  ...UTF-8 intact" "$out" '"body":"Buy beans ☕"'
expect "GET one" "$(bodycode "$B/notes/3")" '{"id":3,"body":"Buy beans ☕"} 200'
expect "PUT replaces it" "$(bodycode -X PUT -H "$J" -d '{"body":"Buy more beans"}' "$B/notes/3")" '"body":"Buy more beans"} 200'
expect "DELETE: 204" "$(curl -si -X DELETE "$B/notes/3")" "204 No Content"
expect "  ...and it's gone: 404" "$(bodycode "$B/notes/3")" "No such note.\"} 404"
expect "PUT on a missing note is a 404" "$(bodycode -X PUT -H "$J" -d '{"body":"x"}' "$B/notes/99")" "404"
expect "a form post isn't JSON: 415" "$(bodycode -d 'body=x' "$B/notes")" "415"
expect "broken JSON: 400" "$(bodycode -H "$J" -d '{nope' "$B/notes")" "400"
expect "an empty body: 422" "$(bodycode -H "$J" -d '{"body":""}' "$B/notes")" "422"
expect "a body that isn't a string: 422" "$(bodycode -H "$J" -d '{"body":["x"]}' "$B/notes")" "422"
out="$(curl -si -X PATCH "$B/notes/1")"
expect "PATCH isn't offered: 405" "$out" "405 Method Not Allowed"
expect "  ...and Allow lists what is" "$out" "Allow: GET, PUT, DELETE"
expect "a path that's no resource: 404" "$(bodycode "$B/nope")" "404"
stop
