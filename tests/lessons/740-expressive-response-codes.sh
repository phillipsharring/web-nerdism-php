# expressive-response-codes: https://phillipharrington.com/php/expressive-response-codes
B="http://localhost:$PORT"
code() { curl -s -o /dev/null -w '%{http_code}' "$@"; }
bodycode() { curl -s -w " %{http_code}" "$@" | tr -d "\n"; }
secs() { curl -s -o /dev/null -w '%{time_total}' "$@"; }

serve expressive-response-codes before.php
expect "before: a stranger learns the email has no account" "$(curl -s -d 'email=nobody@example.com&password=x' "$B/login")" "No account with that email."
expect "  ...and that alice's does" "$(curl -s -d 'email=alice@example.com&password=x' "$B/login")" "Wrong password."
fast="$(secs -d 'email=nobody@example.com&password=x' "$B/login")"
slow="$(secs -d 'email=alice@example.com&password=x' "$B/login")"
if awk "BEGIN { exit !($slow > $fast * 5) }"; then pass "  ...and the timing gives it away too ($fast s vs $slow s)"; else fail "  ...and the timing gives it away too" "$fast vs $slow"; fi
expect "  ...a real login works" "$(curl -s -d 'email=alice@example.com&password=correct horse battery staple' "$B/login")" '"token":"alice-token"'
expect "  ...403 confirms bob's note exists" "$(bodycode -H 'Authorization: Bearer alice-token' "$B/notes/2")" "belongs to someone else.\"} 403"
stop

serve expressive-response-codes after.php
nobody="$(bodycode -d 'email=nobody@example.com&password=x' "$B/login")"
alice="$(bodycode -d 'email=alice@example.com&password=x' "$B/login")"
expect "after: no account gets a vague 401" "$nobody" 'Wrong email or password."} 401'
if [ "$nobody" = "$alice" ]; then pass "  ...exactly the same as a wrong password"; else fail "  ...exactly the same as a wrong password" "$alice"; fi
fast="$(secs -d 'email=nobody@example.com&password=x' "$B/login")"
if awk "BEGIN { exit !($fast > 0.05) }"; then pass "  ...and just as slow ($fast s): the dummy hash"; else fail "  ...and just as slow" "$fast"; fi
expect "  ...a 401 says how to authenticate" "$(curl -si -d 'email=nobody@example.com&password=x' "$B/login")" "WWW-Authenticate: Bearer"
expect "  ...a real login works" "$(curl -s -d 'email=alice@example.com&password=correct horse battery staple' "$B/login")" '"token":"alice-token"'
mine="$(bodycode -H 'Authorization: Bearer alice-token' "$B/notes/2")"
missing="$(bodycode -H 'Authorization: Bearer alice-token' "$B/notes/9")"
expect "  ...someone else's note is a 404" "$mine" 'Note not found."} 404'
if [ "$mine" = "$missing" ]; then pass "  ...the same as a note that doesn't exist"; else fail "  ...the same as a note that doesn't exist" "$missing"; fi
expect "  ...your own note is a 200" "$(bodycode -H 'Authorization: Bearer alice-token' "$B/notes/1")" 'Buy milk"} 200'
expect "  ...no token is a 401" "$(code "$B/notes/1")" "401"
expect "  ...a forged token is a 401" "$(code -H 'Authorization: Bearer admin' "$B/notes/1")" "401"
out="$(curl -si -H 'Authorization: Bearer alice-token' -d 'text=Hello' "$B/notes")"
expect "  ...creating is a 201" "$out" "HTTP/1.1 201 Created"
expect "  ...with a Location" "$out" "Location: /notes/3"
expect "  ...an empty note is a 422" "$(code -H 'Authorization: Bearer alice-token' -d 'text=' "$B/notes")" "422"
expect "  ...an array is not a note" "$(code -H 'Authorization: Bearer alice-token' -d 'text[]=x' "$B/notes")" "422"
out="$(curl -si "$B/login")"
expect "  ...GET /login is a 405" "$out" "405 Method Not Allowed"
expect "  ...with Allow" "$out" "Allow: POST"
expect "  ...and a path that isn't anything is a 404" "$(code "$B/nope")" "404"
stop
