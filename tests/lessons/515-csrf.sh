# csrf: https://phillipharrington.com/php/csrf
csrf_dc() { SITE_PORT=$PORT EVIL_PORT=$((PORT + 1)) docker compose -p "$NAME-csrf" -f "$ROOT/csrf/compose.yml" "$@"; }
csrf_dc up -d >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && curl -s -o /dev/null "http://localhost:$((PORT + 1))/" && break; sleep 0.5; done
jar="$(mktemp)"
site() { curl -s -c "$jar" -b "$jar" "$@"; }

expect "the attacker's page aims a form at the naive page" "$(curl -s "http://localhost:$((PORT + 1))/")" 'action="http://localhost:8000/naive.php"'
expect "  ...and submits it on load" "$(curl -s "http://localhost:$((PORT + 1))/")" "getElementById('prize').submit()"
expect "logged in as Alice" "$(site "http://localhost:$PORT/naive.php")" "Your email address: alice@example.com"
expect "naive: a POST from the other site changes the email" "$(site -H "Origin: http://localhost:$((PORT + 1))" -d 'email=attacker@example.net' "http://localhost:$PORT/naive.php")" "Your email address: attacker@example.net"

expect "fixed: the form carries a token" "$(site "http://localhost:$PORT/")" 'name="csrf" value="'
out="$(site -w '\nstatus %{http_code}' -H "Origin: http://localhost:$((PORT + 1))" -d 'email=attacker2@example.net' "http://localhost:$PORT/")"
expect "fixed: a POST without the token is refused" "$out" "That form didn't come from this site."
expect "  ...with a 403" "$out" "status 403"
expect "fixed: a wrong token is refused" "$(site -w '\nstatus %{http_code}' -d 'csrf=guess&email=attacker2@example.net' "http://localhost:$PORT/")" "status 403"
expect "fixed: an array for a token is refused, not a crash" "$(site -w '\nstatus %{http_code}' -d 'csrf[]=x&email=attacker2@example.net' "http://localhost:$PORT/")" "status 403"
token="$(site "http://localhost:$PORT/" | grep -o 'name="csrf" value="[0-9a-f]*"' | cut -d'"' -f4)"
out="$(site -d "csrf=$token&email=alice@example.com" "http://localhost:$PORT/")"
expect "fixed: the page's own form, with its token, works" "$out" "Changed."
expect "  ...and the email is Alice's again" "$out" "Your email address: alice@example.com"
expect "fixed: with the token, the email is still checked" "$(site -w '\nstatus %{http_code}' -d "csrf=$token&email=not-an-email" "http://localhost:$PORT/")" "status 422"
rm -f "$jar"
csrf_dc down >/dev/null 2>&1
