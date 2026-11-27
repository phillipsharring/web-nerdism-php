# rate-limiting: https://phillipharrington.com/php/rate-limiting
rl_dc() { WEB_PORT=$PORT docker compose -p "$NAME-rate-limiting" -f "$ROOT/rate-limiting/compose.yml" "$@"; }
rl_dc up -d --build --wait >/dev/null 2>&1
try() { curl -s -w '\nstatus %{http_code}' --data-urlencode "email=$2" --data-urlencode "password=$3" "http://localhost:$PORT/$1"; }

out="$(cd "$ROOT/rate-limiting" && sh guess.sh "http://localhost:$PORT/naive.php")"
expect "naive: the guesses find Alice's password" "$out" "200 tigger"
expect "  ...after 43 wrong ones" "$(grep -c '^401 ' <<< "$out")" "43"

expect "fixed: the right password works" "$(try '' alice@example.com tigger)" "Welcome back."
try '' alice@example.com wrong1 >/dev/null; try '' alice@example.com wrong2 >/dev/null
try '' alice@example.com tigger >/dev/null
out="$(cd "$ROOT/rate-limiting" && sh guess.sh "http://localhost:$PORT/")"
expect "fixed: a success clears the count, so ten guesses get a 401" "$(grep -c '^401 ' <<< "$out")" "10"
expect "  ...then everything is a 429" "$out" "429 password1"
expect "  ...even the right password" "$out" "429 tigger"
if grep -qF "200 " <<< "$out"; then fail "  ...and the guesses never get in" "$out"; else pass "  ...and the guesses never get in"; fi
out="$(curl -s -D - --data-urlencode 'email=alice@example.com' --data-urlencode 'password=tigger' "http://localhost:$PORT/")"
expect "fixed: the 429 says when to come back" "$out" "Retry-After: 900"
expect "  ...in words too" "$out" "Too many failed logins. Try again in 15 minutes."

# The address has 10 failures so far. 40 more, on other emails, reach the per-IP limit.
for n in $(seq 1 40); do try '' "nobody$n@example.com" wrong >/dev/null; done
expect "fixed: 50 failures from one address lock out every email from it" "$(try '' "somebody@example.com" wrong)" "status 429"
expect "fixed: an array is refused, not a crash" "$(curl -s -w 'status %{http_code}' -d 'email[]=x&password=y' "http://localhost:$PORT/naive.php")" "status 401"
rl_dc down -v >/dev/null 2>&1
