# project-signup-form: https://phillipharrington.com/php/projects/signup-form
sp() { APP_PORT=$PORT docker compose -p "$NAME-signup-form" -f "$ROOT/project-signup-form/compose.yml" "$@"; }
sp up -d --build >/dev/null 2>&1
app="http://localhost:$PORT"
for _ in $(seq 1 90); do curl -s -o /dev/null "$app/" && break; sleep 1; done
sql() { sp exec -T db mysql -uapp -papp-password course -N -e "$1" 2>/dev/null; }

curl -s -o /dev/null -d 'email=alice@example.com&password=password123' "$app/naive.php"
curl -s -o /dev/null -d 'email=bob@example.com&password=password123' "$app/naive.php"
expect "naive: md5 of password123 is stored" "$(sql "SELECT password_hash FROM users WHERE email = 'alice@example.com'")" "482c811da5d5b4bc6d497ffa98491e38"
expect "naive: the same password, the same hash" "$(sql "SELECT COUNT(DISTINCT password_hash) FROM users WHERE email IN ('alice@example.com', 'bob@example.com')")" "1"
expect "naive: a word list cracks it at once" "$(sp exec -T php php crack.php 482c811da5d5b4bc6d497ffa98491e38)" "Found it: password123"
out="$(sp exec -T php php speed.php)"
if grep -qE 'md5: +[0-9]{1,3}(,[0-9]{3})+ guesses' <<< "$out" && grep -qE 'password_hash: +[0-9]{1,3} guesses' <<< "$out"; then pass "md5 makes millions of guesses a second, bcrypt a handful"; else fail "md5 makes millions of guesses a second, bcrypt a handful" "$out"; fi

expect "fixed: a short password is refused" "$(curl -s -d 'email=carol@example.com&password=password123' "$app/")" "at least 12 characters"
expect "fixed: a common one is refused" "$(curl -s -d 'email=carol@example.com&password=Password1234' "$app/")" "one of the most common passwords"
expect "fixed: a bad address is refused" "$(curl -s -d 'email=carol&password=correct horse battery staple' "$app/")" "An email address, please."
long="$(printf 'é%.0s' {1..40})"
expect "fixed: over 72 bytes is refused" "$(curl -s --data-urlencode "password=$long" -d 'email=carol@example.com' "$app/")" "That password is too long."
expect "fixed: a good signup" "$(curl -si --data-urlencode 'password=correct horse battery staple' -d 'email=carol@example.com' "$app/")" "Location: /?created=1"
curl -s -o /dev/null --data-urlencode 'password=correct horse battery staple' -d 'email=dave@example.com' "$app/"
expect "  ...stores a bcrypt hash, cost 12" "$(sql "SELECT password_hash FROM users WHERE email = 'carol@example.com'")" '$2y$12$'
expect "  ...different for the same password" "$(sql "SELECT COUNT(DISTINCT password_hash) FROM users WHERE email IN ('carol@example.com', 'dave@example.com')")" "2"
hash="$(sql "SELECT password_hash FROM users WHERE email = 'carol@example.com'")"
expect "  ...and password_verify() accepts it" "$(sp exec -T php php -r 'var_dump(password_verify("correct horse battery staple", $argv[1]));' "$hash")" "bool(true)"
expect "fixed: the same address again is refused" "$(curl -s --data-urlencode 'password=another good long password' -d 'email=CAROL@example.com' "$app/")" "That address already has an account."
expect "  ...and only one row exists" "$(sql "SELECT COUNT(*) FROM users WHERE email = 'carol@example.com'")" "1"
sp down -v >/dev/null 2>&1
