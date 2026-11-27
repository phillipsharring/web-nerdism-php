# project-sessions: https://phillipharrington.com/php/projects/sessions
serve project-sessions -t public
app="http://localhost:$PORT"
jar="$(mktemp)"; other="$(mktemp)"
out="$(curl -si -c "$jar" "$app/")"
expect "the first visit starts a session" "$out" "Set-Cookie: PHPSESSID="
expect "  ...with HttpOnly and SameSite" "$out" "; path=/; HttpOnly; SameSite=Lax"
expect "  ...and an empty shortlist" "$out" "Nothing yet."
expect "adding is a 303" "$(curl -si -b "$jar" -c "$jar" -d 'action=add&item=cherry' "$app/")" "HTTP/1.1 303"
out="$(curl -s -b "$jar" -c "$jar" "$app/")"
expect "  ...then a message, once" "$out" "Added Cherry."
expect "  ...and it's on the list" "$out" "<li>Cherry"
expect "the message doesn't come back" "$(curl -s -b "$jar" -c "$jar" "$app/")" "<li>Cherry"
if grep -qF "Added Cherry." <<< "$(curl -s -b "$jar" "$app/")"; then fail "  ...not twice" ""; else pass "  ...not twice"; fi
expect "another visitor has their own list" "$(curl -s -c "$other" "$app/")" "Nothing yet."
id="$(awk '$6 == "PHPSESSID" {print $7}' "$jar")"
expect "the data lives on the server, under the id" "$(docker exec "$NAME" cat "/tmp/sess_$id")" 'shortlist|a:1:{s:6:"cherry";b:1;}'
curl -s -o /dev/null -b "$jar" -c "$jar" -d 'action=add&item=../../etc/passwd' "$app/"
expect "an item that isn't ours is refused" "$(curl -s -b "$jar" -c "$jar" "$app/")" "Pick something from the list."
out="$(curl -si -b 'PHPSESSID=madeup123' "$app/")"
if grep -q 'Set-Cookie: PHPSESSID=' <<< "$out" && ! grep -qF 'PHPSESSID=madeup123' <<< "$out"; then pass "a made-up id gets a new one (strict mode)"; else fail "a made-up id gets a new one (strict mode)" "$out"; fi
out="$(curl -si -b "$jar" -c "$jar" -d 'action=start-over' "$app/")"
expect "start over deletes the cookie" "$out" "Set-Cookie: PHPSESSID=deleted; expires=Thu, 01 Jan 1970"
if docker exec "$NAME" test -e "/tmp/sess_$id"; then fail "  ...and the file on the server" ""; else pass "  ...and the file on the server"; fi
rm -f "$jar" "$other"
stop
