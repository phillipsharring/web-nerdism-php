# inserting-data: https://phillipharrington.com/php/inserting-data
in_dc() { docker compose -p "$NAME-inserting-data" -f "$ROOT/inserting-data/compose.yml" "$@"; }
in_dc build -q php >/dev/null 2>&1
in_dc up -d --wait db >/dev/null 2>&1
in_q() { in_dc exec -T db mysql -u app -papp-password course -e "$1" 2>&1; }
in_php() { in_dc run --rm --no-deps -T php php "$@" 2>&1; }
expect "a new subscriber gets id 1" "$(in_php subscribe.php alice@example.com Alice)" "Added subscriber #1"
expect "  ...and the next, 2" "$(in_php subscribe.php bo@example.com Bo)" "Added subscriber #2"
expect "the same email again updates the name" "$(in_php subscribe.php alice@example.com 'Alice Rivera')" "Updated alice@example.com"
expect "  ...and again changes nothing" "$(in_php subscribe.php alice@example.com 'Alice Rivera')" "alice@example.com was already subscribed, as Alice Rivera"
expect "the upserts used up ids 3 and 4" "$(in_php subscribe.php cy@example.com Cy)" "Added subscriber #5"
out="$(in_q 'SELECT `id`, `email`, `name` FROM `subscribers`;')"
expect "  ...the table has 1, 2 and 5" "$(cut -f1 <<< "$out" | tail -3 | tr '\n' ' ')" "1 2 5"
expect "  ...and Alice's new name" "$out" "1	alice@example.com	Alice Rivera"
expect "it refuses something that isn't an email" "$(in_php subscribe.php nope Mallory)" "Usage: php subscribe.php"
expect "capitals count as the same email" "$(in_php subscribe.php ALICE@EXAMPLE.COM 'Alice R.')" "Updated ALICE@EXAMPLE.COM"
expect "INSERT IGNORE turns the duplicate into a warning" "$(in_q "INSERT IGNORE INTO \`subscribers\` (\`email\`, \`name\`) VALUES ('bo@example.com', 'Robert'); SHOW WARNINGS;")" "Warning	1062	Duplicate entry"
in_q "INSERT INTO \`subscribers\` (\`email\`, \`name\`) VALUES ('dee@example.com', 'Dee'), ('eve@example.com', 'Eve');" >/dev/null
expect "one INSERT adds many rows" "$(in_q 'SELECT COUNT(*) FROM `subscribers`;')" "5"
in_dc down -v >/dev/null 2>&1
