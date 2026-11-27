# sql: https://phillipharrington.com/php/sql
sql_dc() { docker compose -p "$NAME-sql" -f "$ROOT/sql/compose.yml" "$@"; }
sql_dc build -q php >/dev/null 2>&1
sql_dc up -d --wait db >/dev/null 2>&1
sql_q() { sql_dc exec -T db mysql -u app -papp-password course -e "$1" 2>&1; }
sql_php() { sql_dc run --rm --no-deps -T php php "$@" 2>&1; }
expect "SELECT * finds the seven books" "$(sql_q 'SELECT COUNT(*) AS `n` FROM `books`;')" "7"
out="$(sql_q 'SELECT `title`, `year` FROM `books` WHERE `year` < 1900 ORDER BY `year`;')"
expect "WHERE and ORDER BY: oldest first" "$(head -3 <<< "$out" | tail -1)" "Pride and Prejudice	1813"
if grep -qF "Kindred" <<< "$out"; then fail "  ...and nothing after 1900" "$out"; else pass "  ...and nothing after 1900"; fi
sql_q "INSERT INTO \`books\` (\`title\`, \`author\`, \`year\`) VALUES ('Beloved', 'Toni Morrison', 1986);" >/dev/null
sql_q "UPDATE \`books\` SET \`year\` = 1987 WHERE \`title\` = 'Beloved';" >/dev/null
expect "INSERT, then UPDATE" "$(sql_q "SELECT \`year\` FROM \`books\` WHERE \`title\` = 'Beloved';")" "1987"
sql_q "DELETE FROM \`books\` WHERE \`title\` = 'Beloved';" >/dev/null
expect "  ...then DELETE" "$(sql_q 'SELECT COUNT(*) AS `n` FROM `books`;')" "7"
out="$(sql_php books.php 1900)"
expect "books.php lists the books before 1900" "$out" "1813  Pride and Prejudice, by Jane Austen"
expect "  ...with the accent intact" "$out" "Charlotte Brontë"
if grep -qF "Kindred" <<< "$out"; then fail "  ...and none after" "$out"; else pass "  ...and none after"; fi
expect "it refuses a year that isn't one" "$(sql_php books.php '1900 OR 1=1')" "Usage: php books.php YEAR"
sql_dc down -v >/dev/null 2>&1
