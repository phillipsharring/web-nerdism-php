# project-profile-page: https://phillipharrington.com/php/projects/profile-page
pp() { APP_PORT=$PORT ATTACKER_PORT=$((PORT + 1)) docker compose -p "$NAME-profile-page" -f "$ROOT/project-profile-page/compose.yml" "$@"; }
pp up -d --build >/dev/null 2>&1
app="http://localhost:$PORT"
for _ in $(seq 1 90); do curl -s -o /dev/null "$app/login.php" && break; sleep 1; done
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$((PORT + 1))/" && break; sleep 0.5; done
sql() { pp exec -T db mysql -uapp -papp-password course -N -e "$1" 2>/dev/null; }
jar="$(mktemp)"

expect "a stranger is sent to sign in" "$(curl -si "$app/profile.php")" "Location: /login.php"
curl -s -o /dev/null -c "$jar" "$app/login.php"
expect "signing in goes to the profile" "$(curl -si -b "$jar" -c "$jar" --data-urlencode 'password=correct horse battery staple' -d 'email=alice@example.com' "$app/login.php")" "Location: /profile.php"
expect "the profile shows" "$(curl -s -b "$jar" "$app/profile.php")" "<h1>Alice</h1>"

page="$(curl -s "http://localhost:$((PORT + 1))/")"
expect "the attacker's page posts to the naive profile" "$page" 'action="http://localhost:8000/naive-profile.php"'
# The browser's part of the attack: the attacker's form, sent with Alice's cookie.
curl -s -o /dev/null -b "$jar" -H "Origin: http://localhost:$((PORT + 1))" -d 'display_name=Hacked&bio=Buy cheap watches at https://spam.example' "$app/naive-profile.php"
expect "naive: the forged form changed Alice's profile" "$(sql 'SELECT display_name FROM users WHERE id = 1')" "Hacked"

sql "UPDATE users SET display_name = 'Alice' WHERE id = 1"
out="$(curl -si -b "$jar" -H "Origin: http://localhost:$((PORT + 1))" -d 'display_name=Hacked&bio=spam' "$app/profile.php")"
expect "fixed: the same forged form is a 403" "$out" "HTTP/1.1 403"
expect "  ...with a polite note" "$out" "That form had expired."
expect "  ...and the profile is unchanged" "$(sql 'SELECT display_name FROM users WHERE id = 1')" "Alice"
expect "fixed: a made-up token is a 403 too" "$(curl -si -b "$jar" -d 'csrf=0000&display_name=Hacked&bio=spam' "$app/profile.php")" "HTTP/1.1 403"
expect "fixed: an array for a token is a 403" "$(curl -si -g -b "$jar" -d 'csrf[]=x&display_name=Hacked' "$app/profile.php")" "HTTP/1.1 403"
token="$(curl -s -b "$jar" "$app/profile.php" | sed -n 's/.*name="csrf" value="\([0-9a-f]*\)".*/\1/p')"
if [ "${#token}" -eq 64 ]; then pass "fixed: the form carries a 64-character token"; else fail "fixed: the form carries a 64-character token" "$token"; fi
expect "fixed: the real form, with its token, saves" "$(curl -si -b "$jar" --data-urlencode 'display_name=Alice A.' --data-urlencode 'bio=<b>Hi</b>' -d "csrf=$token" "$app/profile.php")" "Location: /profile.php"
out="$(curl -s -b "$jar" "$app/profile.php")"
expect "  ...shows it once saved" "$out" "<h1>Alice A.</h1>"
expect "  ...escaped" "$out" "&lt;b&gt;Hi&lt;/b&gt;"
expect "fixed: an empty name is refused" "$(curl -s -b "$jar" -d "csrf=$token&display_name=&bio=x" "$app/profile.php")" "A display name, please"
other="$(mktemp)"
curl -s -o /dev/null -c "$other" "$app/login.php"
curl -s -o /dev/null -b "$other" -c "$other" --data-urlencode 'password=correct horse battery staple' -d 'email=alice@example.com' "$app/login.php"
expect "fixed: one session's token doesn't work in another" "$(curl -si -b "$other" -d "csrf=$token&display_name=Hacked&bio=x" "$app/profile.php")" "HTTP/1.1 403"
rm -f "$jar" "$other"
pp down -v >/dev/null 2>&1
