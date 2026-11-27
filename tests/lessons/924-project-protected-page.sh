# project-protected-page: https://phillipharrington.com/php/projects/protected-page
serve project-protected-page -t public
app="http://localhost:$PORT"
jar="$(mktemp)"
out="$(curl -si "$app/members.php")"
expect "a stranger is sent to sign in" "$out" "Location: /login.php?next=%2Fmembers.php"
if grep -qF "Thursday" <<< "$out"; then fail "  ...and sees nothing of the page" "$out"; else pass "  ...and sees nothing of the page"; fi
expect "the sign-in page keeps where to go next" "$(curl -s -c "$jar" "$app/login.php?next=%2Fmembers.php")" 'name="next" value="/members.php"'
out="$(curl -s -b "$jar" -c "$jar" -d 'email=alice@example.com&password=wrong&next=/members.php' "$app/login.php")"
expect "a wrong password gets the vague answer" "$out" "Wrong email or password."
expect "an unknown address gets the same one" "$(curl -s -d 'email=nobody@example.com&password=wrong' "$app/login.php")" "Wrong email or password."
before="$(awk '$6 == "PHPSESSID" {print $7}' "$jar")"
out="$(curl -si -b "$jar" -c "$jar" --data-urlencode 'password=correct horse battery staple' -d 'email=alice@example.com&next=/members.php' "$app/login.php")"
expect "the right password goes back to the page" "$out" "Location: /members.php"
after="$(awk '$6 == "PHPSESSID" {print $7}' "$jar")"
if [ -n "$after" ] && [ "$before" != "$after" ]; then pass "  ...with a new session id"; else fail "  ...with a new session id" "$before / $after"; fi
out="$(curl -si -b "$jar" -c "$jar" "$app/members.php")"
expect "signed in, the page shows" "$out" "Hello, alice@example.com. The meeting is Thursday at 7."
expect "  ...marked private, no-store" "$out" "Cache-Control: private, no-store"
expect "a foreign next is ignored" "$(curl -si -b "$jar" -c "$jar" --data-urlencode 'password=correct horse battery staple' -d 'email=alice@example.com&next=https://evil.example/' "$app/login.php")" "Location: /members.php"
expect "signing out by link is refused" "$(curl -si -b "$jar" "$app/logout.php")" "HTTP/1.1 405"
expect "signing out by POST works" "$(curl -si -b "$jar" -c "$jar" -X POST "$app/logout.php")" "Location: /"
expect "  ...and the page is closed again" "$(curl -si -b "$jar" "$app/members.php")" "Location: /login.php"
out="$(curl -si "$app/forgot-exit.php")"
expect "the guard without exit redirects" "$out" "Location: /login.php"
expect "  ...and sends the secret anyway" "$out" "The meeting is Thursday at 7."
rm -f "$jar"
stop
