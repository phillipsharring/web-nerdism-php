# sessions-and-cookie-flags: https://phillipharrington.com/php/sessions-and-cookie-flags
serve sessions-and-cookie-flags
login() { curl -s -b "PHPSESSID=$2" --data-urlencode 'email=alice@example.com' --data-urlencode "password=$3" "http://localhost:$PORT/$1"; }
as() { curl -s -b "PHPSESSID=$2" "http://localhost:$PORT/$1"; }
good='correct horse battery staple'

out="$(curl -sI "http://localhost:$PORT/naive.php" | grep -i '^set-cookie')"
expect "naive: PHP's default cookie" "$out" "; path=/"
if grep -qiE 'secure|httponly|samesite' <<< "$out"; then fail "  ...with no flags" "$out"; else pass "  ...with no flags"; fi
expect "fixed: the cookie has all three flags" "$(curl -sI "http://localhost:$PORT/" | grep -i '^set-cookie')" "; path=/; secure; HttpOnly; SameSite=Lax"

expect "naive: an id the attacker picked is accepted" "$(curl -s -D - -b 'PHPSESSID=attacker-picked-1' "http://localhost:$PORT/naive.php" | grep -ci '^set-cookie')" "0"
expect "naive: the victim logs in with it" "$(login naive.php attacker-picked-1 "$good")" "Logged in as alice@example.com."
expect "naive: the attacker, with the same id, is logged in as her" "$(as naive.php attacker-picked-1)" "Logged in as alice@example.com."

expect "fixed: an id PHP didn't make is replaced" "$(curl -s -D - -b 'PHPSESSID=attacker-picked-2' "http://localhost:$PORT/" | grep -i '^set-cookie')" "Set-Cookie: PHPSESSID="
login '' attacker-picked-2 "$good" >/dev/null
expect "fixed: the attacker's id isn't logged in" "$(as '' attacker-picked-2)" "Not logged in."
# Strict mode accepts an id that already exists. Regenerating at login still saves the day.
as naive.php attacker-picked-3 >/dev/null
out="$(curl -s -D - -b 'PHPSESSID=attacker-picked-3' --data-urlencode 'email=alice@example.com' --data-urlencode "password=$good" "http://localhost:$PORT/")"
expect "fixed: logging in issues a new id" "$out" "Set-Cookie: PHPSESSID="
expect "  ...and logs the victim in" "$out" "Logged in as alice@example.com."
expect "  ...while the old id knows nothing" "$(as '' attacker-picked-3)" "Not logged in."
expect "fixed: a wrong password is a 401" "$(curl -s -o /dev/null -w 'status %{http_code}' --data-urlencode 'email=alice@example.com' --data-urlencode 'password=sunshine' "http://localhost:$PORT/")" "status 401"
expect "fixed: an array for a password is refused, not a crash" "$(curl -s -w 'status %{http_code}' -d 'email=alice@example.com&password[]=x' "http://localhost:$PORT/")" "status 401"
stop
