# creating-tables: https://phillipharrington.com/php/creating-tables
ct_dc() { docker compose -p "$NAME-creating-tables" -f "$ROOT/creating-tables/compose.yml" "$@"; }
ct_dc build -q php >/dev/null 2>&1
ct_dc up -d --wait db >/dev/null 2>&1
ct_q() { ct_dc exec -T db mysql -u app -papp-password course -e "$1" 2>&1; }
ct_php() { ct_dc run --rm --no-deps -T php php "$@" 2>&1; }
ct_ins() { ct_q "INSERT INTO \`books\` (\`title\`, \`format\`, \`price_cents\`) VALUES ($1);"; }
expect "DESCRIBE shows the columns" "$(ct_q 'DESCRIBE `books`;')" "price_cents	int unsigned	NO"
ct_ins "'Frankenstein', 'paperback', 1299" >/dev/null
out="$(ct_q 'SELECT * FROM `books`;')"
expect "the database fills in the id, the NULLs and the time" "$out" "1	Frankenstein	paperback	NULL	1299	NULL	20"
expect "NOT NULL refuses a NULL" "$(ct_ins "NULL, 'paperback', 1299")" "Column 'title' cannot be null"
expect "UNSIGNED refuses a negative" "$(ct_ins "'Dracula', 'paperback', -1")" "Out of range value for column 'price_cents'"
expect "VARCHAR(200) refuses 201 characters" "$(ct_ins "REPEAT('x', 201), 'paperback', 1299")" "Data too long for column 'title'"
ct_ins "'Dracula', 'scroll', 999" >/dev/null
expect "the table accepts a format that isn't on the list" "$(ct_q 'SELECT `format` FROM `books` WHERE `id` = 2;')" "scroll"
ct_ins "'Dracula', 'paperback', 12.99" >/dev/null
expect "a decimal in an INT column is rounded, not refused" "$(ct_q 'SELECT `price_cents` FROM `books` WHERE `id` = 3;')" "13"
expect "add-book.php adds a good one" "$(ct_php add-book.php 'Jane Eyre' ebook 899)" "Added book #4"
out="$(ct_php add-book.php 'Jane Eyre' scroll 8.99)"
expect "  ...and refuses the format" "$out" "A format: hardcover, paperback, ebook."
expect "  ...and the decimal price" "$out" "A price in cents"
expect "strict mode is on" "$(ct_q 'SELECT @@sql_mode;')" "STRICT_TRANS_TABLES"
ct_dc down -v >/dev/null 2>&1
