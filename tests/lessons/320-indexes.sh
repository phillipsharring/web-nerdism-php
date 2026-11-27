# indexes: https://phillipharrington.com/php/indexes
ix_dc() { docker compose -p "$NAME-indexes" -f "$ROOT/indexes/compose.yml" "$@"; }
ix_dc build -q php >/dev/null 2>&1
ix_dc up -d --wait db >/dev/null 2>&1
ix_q() { ix_dc exec -T db mysql -u app -papp-password course -e "$1" 2>&1; }
ix_php() { ix_dc run --rm --no-deps -T php php "$@" 2>&1; }
ix_explain() { ix_q "EXPLAIN SELECT \`id\`, \`name\` FROM \`customers\` WHERE $1;" | tail -1 | cut -f5,7,10; }
expect "init.sql made 200,000 customers" "$(ix_q 'SELECT COUNT(*) FROM `customers`;')" "200000"
expect "find.php finds one" "$(ix_php find.php user123456@example.com)" "Found Customer 123456 (#123456)."
out="$(ix_explain "\`email\` = 'user123456@example.com'")"
expect "without an index: a full table scan" "$out" "ALL"
rows="$(cut -f3 <<< "$out")"
if [ "$rows" -gt 150000 ]; then pass "  ...expecting to read about 200,000 rows"; else fail "  ...expecting to read about 200,000 rows" "$out"; fi
ix_q 'CREATE UNIQUE INDEX `customers_email_unique` ON `customers` (`email`);' >/dev/null
expect "with the index: one row, by the index" "$(ix_explain "\`email\` = 'user123456@example.com'")" "const	customers_email_unique	1"
expect "find.php still finds her" "$(ix_php find.php user123456@example.com)" "Found Customer 123456"
expect "LOWER() hides the column from its index" "$(ix_explain "LOWER(\`email\`) = 'user123456@example.com'")" "ALL"
expect "a leading wildcard can't use it" "$(ix_explain "\`email\` LIKE '%123456@example.com'")" "ALL"
expect "a prefix can" "$(ix_explain "\`email\` LIKE 'user12345%'")" "range	customers_email_unique"
expect "capitals find her anyway, by the index" "$(ix_explain "\`email\` = 'USER123456@EXAMPLE.COM'")" "const	customers_email_unique	1"
expect "the unique index refuses a duplicate" "$(ix_q "INSERT INTO \`customers\` (\`email\`, \`name\`, \`city\`, \`created_at\`) VALUES ('user1@example.com', 'X', 'Y', NOW());")" "Duplicate entry 'user1@example.com'"
expect "find.php refuses something that isn't an email" "$(ix_php find.php "x' OR '1'='1")" "Usage: php find.php EMAIL"
ix_dc down -v >/dev/null 2>&1
