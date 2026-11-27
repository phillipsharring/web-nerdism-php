# sessions: https://phillipharrington.com/php/sessions
serve sessions
jar="$(mktemp)"
out="$(curl -si -c "$jar" -b "$jar" "http://localhost:$PORT/")"
expect "the first visit sets a session cookie" "$out" "Set-Cookie: PHPSESSID="
expect "  ...HttpOnly and SameSite" "$out" "HttpOnly; SameSite=Lax"
expect "  ...and counts one" "$out" "Visit number 1."
out="$(curl -s -c "$jar" -b "$jar" "http://localhost:$PORT/")"
expect "the cookie brings the count back" "$out" "Visit number 2."
id="$(sed -n 's/^Session id: //p' <<< "$out")"
expect "the data lives in a file on the server" "$(docker exec "$NAME" cat "/tmp/sess_$id")" "visits|i:2;"
out="$(curl -si -b 'PHPSESSID=madeup' "http://localhost:$PORT/")"
expect "a made-up id starts over" "$out" "Visit number 1."
if grep -qF "PHPSESSID=madeup" <<< "$out"; then fail "  ...with a new id, not the made-up one" "$out"; else pass "  ...with a new id, not the made-up one"; fi
rm -f "$jar"
stop
