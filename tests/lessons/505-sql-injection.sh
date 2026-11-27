# sql-injection: https://phillipharrington.com/php/sql-injection
sqli_dc() { WEB_PORT=$PORT docker compose -p "$NAME-sql-injection" -f "$ROOT/sql-injection/compose.yml" "$@"; }
sqli_dc up -d --build --wait >/dev/null 2>&1
sqli() { curl -s --get --data-urlencode "email=$2" "http://localhost:$PORT/$1"; }
union="' UNION SELECT CONCAT(email, ' ', password_hash) FROM members -- -"

expect "naive: an honest lookup" "$(sqli naive.php 'alice@example.com')" "Found: Alice"
expect "naive: one quote breaks the query" "$(sqli naive.php "'")" "You have an error in your SQL syntax"
out="$(sqli naive.php "' OR '1'='1")"
expect "naive: ' OR '1'='1 finds everyone" "$out" "Found: Bob"
expect "  ...accent intact" "$out" "Found: Chloé"
out="$(sqli naive.php "$union")"
expect "naive: UNION hands over the emails" "$out" "Found: bob@example.com"
expect "  ...and the password hashes" "$out" 'Found: alice@example.com $2y$12$'
expect "naive: two statements in one query are refused" "$(sqli naive.php "'; DROP TABLE members; -- -")" "near 'DROP TABLE members"
expect "  ...so the table is still there" "$(sqli naive.php 'bob@example.com')" "Found: Bob"

expect "fixed: an honest lookup" "$(sqli '' 'chloe@example.com')" "Found: Chloé"
for payload in "'" "' OR '1'='1" "$union"; do
    out="$(sqli '' "$payload")"
    expect "fixed: $payload is just an email that isn't there" "$out" "No member with that email."
    if grep -qF 'Found:' <<< "$out"; then fail "  ...and finds nobody" "$out"; else pass "  ...and finds nobody"; fi
done
expect "fixed: an array is refused" "$(curl -s "http://localhost:$PORT/?email[]=x")" "One email address, please."
sqli_dc down -v >/dev/null 2>&1
