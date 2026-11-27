# good-variable-names: https://phillipharrington.com/php/good-variable-names
before="$(cli good-variable-names before.php)"
expect "before finds the two to remind" "$before" "Remind: alice@example.com, carol@example.com"
if [ "$before" = "$(cli good-variable-names after.php)" ]; then pass "  ...and after, renamed, finds the same"; else fail "  ...and after, renamed, finds the same" "$(cli good-variable-names after.php)"; fi
serve good-variable-names
expect "extract(): a guest by default" "$(curl -s "http://localhost:$PORT/extract.php")" "Welcome, guest."
expect "  ...and an administrator by query string" "$(curl -s "http://localhost:$PORT/extract.php?isAdmin=1")" "Welcome, administrator."
stop
