# project-affiliate-program: https://phillipharrington.com/php/projects/affiliate-program
af() { APP_PORT=$PORT docker compose -p "$NAME-affiliate" -f "$ROOT/project-affiliate-program/compose.yml" "$@"; }
sql() { af exec -T db mysql -uapp -papp-password -N course -e "$1" 2>/dev/null; }
af up -d --build --wait >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
U="http://localhost:$PORT"
out="$(curl -si "$U/?ref=ALICE10")"
expect "an affiliate link sets the cookie" "$out" "Set-Cookie: ref=ALICE10;"
expect "  ...for 30 days" "$out" "Max-Age=2592000"
expect "  ...HttpOnly and SameSite=Lax" "$out" "HttpOnly; SameSite=Lax"
expect "  ...and redirects to the clean page" "$out" "Location: /"
expect "  ...and counts the click" "$(sql "SELECT COUNT(*) FROM clicks")" "1"
out="$(curl -si "$U/?ref=NOTREAL")"
if grep -qF "Set-Cookie" <<< "$out"; then fail "an unknown code sets no cookie" "$out"; else pass "an unknown code sets no cookie"; fi
if grep -qF "Set-Cookie" <<< "$(curl -si "$U/?ref=OLDPARTNER")"; then fail "an inactive affiliate sets no cookie" ""; else pass "an inactive affiliate sets no cookie"; fi
if grep -qF "Set-Cookie" <<< "$(curl -si "$U/?ref=%3Cscript%3E")"; then fail "junk sets no cookie" ""; else pass "junk sets no cookie"; fi
expect "  ...and no click is counted for any of them" "$(sql "SELECT COUNT(*) FROM clicks")" "1"
expect "the redirect never goes anywhere else" "$(curl -si "$U/?ref=ALICE10&next=https://evil.example")" "Location: /"$'\r'
buy() { curl -s -o /dev/null "$@" "$U/buy.php"; }
buy -H 'Cookie: ref=ALICE10' -d email=carol@example.com
expect "an order with the cookie earns 10%, rounded down" "$(sql "SELECT c.amount_cents, a.code FROM commissions c JOIN affiliates a ON a.id = c.affiliate_id")" "499	ALICE10"
buy -H 'Cookie: ref=BOBREVIEWS' -d email=dan@example.com
expect "7.5% of \$49.99 is 374 cents" "$(sql "SELECT amount_cents FROM commissions ORDER BY id DESC LIMIT 1")" "374"
buy -d email=erin@example.com
expect "an order without the cookie earns nothing" "$(sql "SELECT COUNT(*) FROM commissions"), $(sql "SELECT affiliate_id IS NULL FROM orders ORDER BY id DESC LIMIT 1")" "2, 1"
buy -H 'Cookie: ref=ALICE10' -d email=ALICE@example.com
expect "an affiliate buying through their own link earns nothing" "$(sql "SELECT COUNT(*) FROM commissions")" "2"
buy -H 'Cookie: ref=OLDPARTNER' -d email=frank@example.com
buy -H "Cookie: ref=ALICE10' OR '1'='1" -d email=gus@example.com
expect "a forged or stale cookie earns nothing" "$(sql "SELECT COUNT(*) FROM commissions")" "2"
expect "the report adds it up" "$(af exec -T app php bin/report.php)" "ALICE10            2       1      \$4.99"
af down -v >/dev/null 2>&1
