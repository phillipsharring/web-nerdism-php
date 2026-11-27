# project-login-form: https://phillipharrington.com/php/projects/login-form
lp() { APP_PORT=$PORT docker compose -p "$NAME-login-form" -f "$ROOT/project-login-form/compose.yml" "$@"; }
lp up -d --build >/dev/null 2>&1
app="http://localhost:$PORT"
for _ in $(seq 1 90); do curl -s -o /dev/null "$app/" && break; sleep 1; done
pw='password=correct horse battery staple'

# Session fixation, naive: the attacker picks the id, the victim signs in with it.
curl -s -o /dev/null -b 'PHPSESSID=attacker-chosen-1' --data-urlencode "$pw" -d 'email=alice@example.com' "$app/naive-login.php"
expect "naive: the attacker's own id is now signed in" "$(curl -s -b 'PHPSESSID=attacker-chosen-1' "$app/account.php")" "Signed in as alice@example.com."

# Session fixation, fixed.
out="$(curl -si -b 'PHPSESSID=attacker-chosen-2' --data-urlencode "$pw" -d 'email=alice@example.com' "$app/login.php")"
expect "fixed: signing in works" "$out" "Location: /account.php"
if grep -q 'Set-Cookie: PHPSESSID=' <<< "$out" && ! grep -qF 'attacker-chosen-2' <<< "$(grep 'Set-Cookie' <<< "$out")"; then pass "  ...with an id of the server's own"; else fail "  ...with an id of the server's own" "$out"; fi
expect "  ...and the attacker's id is signed in as nobody" "$(curl -s -b 'PHPSESSID=attacker-chosen-2' "$app/account.php")" "Not signed in."
jar="$(mktemp)"
curl -s -o /dev/null -c "$jar" "$app/login.php"
before="$(awk '$6 == "PHPSESSID" {print $7}' "$jar")"
curl -s -o /dev/null -b "$jar" -c "$jar" --data-urlencode "$pw" -d 'email=alice@example.com' "$app/login.php"
after="$(awk '$6 == "PHPSESSID" {print $7}' "$jar")"
if [ -n "$before" ] && [ -n "$after" ] && [ "$before" != "$after" ]; then pass "  ...even an honest id changes at sign-in"; else fail "  ...even an honest id changes at sign-in" "$before / $after"; fi
expect "  ...and the new one is signed in" "$(curl -s -b "$jar" "$app/account.php")" "Signed in as alice@example.com."
rm -f "$jar"

expect "fixed: a wrong password gets the vague answer" "$(curl -s -d 'email=alice@example.com&password=nope' "$app/login.php")" "Wrong email or password."
expect "fixed: so does an unknown address" "$(curl -s --data-urlencode "$pw" -d 'email=nobody@example.com' "$app/login.php")" "Wrong email or password."

# Guessing.
expect "naive: a guessing script gets in" "$(lp exec -T php php guess.php http://localhost:8000/naive-login.php alice@example.com)" "Got in."
out="$(lp exec -T php php guess.php http://localhost:8000/login.php alice@example.com)"
expect "fixed: the same script is stopped" "$out" "Didn't get in."
expect "  ...which starts out getting answers" "$out" " 1  123456                         HTTP/1.1 200 OK"
expect "  ...then a 429, ten failures in at most" "$out" "11  baseball                       HTTP/1.1 429"
expect "  ...even for the right password" "$out" "15  correct horse battery staple   HTTP/1.1 429"
out="$(curl -s --data-urlencode "$pw" -d 'email=alice@example.com' "$app/login.php")"
expect "  ...and from a browser too" "$out" "Too many attempts. Please wait 15 minutes and try again."
# Fill up this IP's fifty with other addresses, until the door shuts.
got=""
for i in $(seq 1 60); do
    code="$(curl -s -o /dev/null -w '%{http_code}' -d "email=user$i@example.com&password=nope" "$app/login.php")"
    [ "$code" = "429" ] && { got=yes; break; }
done
failures="$(lp exec -T db mysql -uapp -papp-password course -N -e 'SELECT MAX(n) FROM (SELECT COUNT(*) AS n FROM login_failures GROUP BY ip) AS per_ip' 2>/dev/null)"
if [ -n "$got" ] && [ "$failures" = "50" ]; then pass "fixed: fifty failures from one IP close it for every address"; else fail "fixed: fifty failures from one IP close it for every address" "got=$got failures=$failures"; fi
lp down -v >/dev/null 2>&1
