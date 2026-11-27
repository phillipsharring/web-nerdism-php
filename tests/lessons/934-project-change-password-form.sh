# project-change-password-form: https://phillipharrington.com/php/projects/change-password-form
cpf() { APP_PORT=$PORT ATTACKER_PORT=$((PORT + 1)) docker compose -p "$NAME-change-password-form" -f "$ROOT/project-change-password-form/compose.yml" "$@"; }
cpf up -d --build >/dev/null 2>&1
app="http://localhost:$PORT"
for _ in $(seq 1 90); do curl -s -o /dev/null "$app/login.php" && break; sleep 1; done
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$((PORT + 1))/" && break; sleep 0.5; done
sql() { cpf exec -T db mysql -uapp -papp-password course -N -e "$1" 2>/dev/null; }
original='$2y$12$noC7DVSxqpYFmE7NBKN48O9sDfYQ9/T8LfV0RjNOgF34NYMDqvv1q'
signin() { # signin JAR PASSWORD
    curl -s -o /dev/null -c "$1" "$app/login.php"
    curl -si -b "$1" -c "$1" --data-urlencode "password=$2" -d 'email=alice@example.com' "$app/login.php"
}
token() { curl -s -b "$1" "$app/change-password.php" | sed -n 's/.*name="csrf" value="\([0-9a-f]*\)".*/\1/p'; }
a="$(mktemp)"; b="$(mktemp)"

expect "signing in works" "$(signin "$a" 'correct horse battery staple')" "Location: /account.php"
expect "the attacker's page posts to the naive form" "$(curl -s "http://localhost:$((PORT + 1))/")" 'action="http://localhost:8000/naive-change-password.php"'
curl -s -o /dev/null -b "$a" -H "Origin: http://localhost:$((PORT + 1))" --data-urlencode 'new_password=the attacker knows this one' "$app/naive-change-password.php"
expect "naive: the forged form changed the password" "$(signin "$b" 'the attacker knows this one')" "Location: /account.php"
expect "  ...and the real one no longer works" "$(signin "$b" 'correct horse battery staple')" "Wrong email or password."

sql "UPDATE users SET password_hash = '$original' WHERE id = 1; DELETE FROM login_failures"
signin "$a" 'correct horse battery staple' >/dev/null
signin "$b" 'correct horse battery staple' >/dev/null
t="$(token "$a")"
out="$(curl -si -b "$a" -d 'new_password=the attacker knows this one&confirm_password=the attacker knows this one' "$app/change-password.php")"
expect "fixed: no token is a 403" "$out" "HTTP/1.1 403"
expect "fixed: with the token but no current password, refused" "$(curl -s -b "$a" --data-urlencode 'new_password=a brand new passphrase' --data-urlencode 'confirm_password=a brand new passphrase' -d "csrf=$t" "$app/change-password.php")" "Your current password is wrong."
expect "  ...and counted as a failed sign-in" "$(sql "SELECT COUNT(*) FROM login_failures WHERE email = 'alice@example.com'")" "1"
cur='current_password=correct horse battery staple'
expect "fixed: a short new password is refused" "$(curl -s -b "$a" --data-urlencode "$cur" -d "csrf=$t&new_password=short&confirm_password=short" "$app/change-password.php")" "at least 12 characters"
expect "fixed: a mismatch is refused" "$(curl -s -b "$a" --data-urlencode "$cur" --data-urlencode 'new_password=a brand new passphrase' --data-urlencode 'confirm_password=a brand new passphrose' -d "csrf=$t" "$app/change-password.php")" "The new passwords don&#039;t match."
expect "fixed: the same password is refused" "$(curl -s -b "$a" --data-urlencode "$cur" --data-urlencode 'new_password=correct horse battery staple' --data-urlencode 'confirm_password=correct horse battery staple' -d "csrf=$t" "$app/change-password.php")" "the password you have now"
before="$(awk '$6 == "PHPSESSID" {print $7}' "$a")"
out="$(curl -si -b "$a" -c "$a" --data-urlencode "$cur" --data-urlencode 'new_password=a brand new passphrase' --data-urlencode 'confirm_password=a brand new passphrase' -d "csrf=$t" "$app/change-password.php")"
expect "fixed: everything right changes it" "$out" "Location: /account.php"
after="$(awk '$6 == "PHPSESSID" {print $7}' "$a")"
if [ "$before" != "$after" ]; then pass "  ...with a new session id"; else fail "  ...with a new session id" "$before"; fi
expect "  ...this session stays signed in" "$(curl -s -b "$a" "$app/account.php")" "Password changed. Any other devices have been signed out."
expect "  ...the other session is signed out" "$(curl -si -b "$b" "$app/account.php")" "Location: /login.php"
expect "  ...the old token is spent" "$(curl -si -b "$a" --data-urlencode 'current_password=a brand new passphrase' -d "csrf=$t&new_password=x&confirm_password=x" "$app/change-password.php")" "HTTP/1.1 403"
expect "  ...the old password no longer works" "$(signin "$b" 'correct horse battery staple')" "Wrong email or password."
expect "  ...and the new one does" "$(signin "$b" 'a brand new passphrase')" "Location: /account.php"
rm -f "$a" "$b"
cpf down -v >/dev/null 2>&1
