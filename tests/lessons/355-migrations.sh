# migrations: https://phillipharrington.com/php/migrations
mi_dc() { docker compose -p "$NAME-migrations" -f "$ROOT/migrations/compose.yml" "$@"; }
mi_dc build -q php >/dev/null 2>&1
mi_dc up -d --wait db >/dev/null 2>&1
mi_q() { mi_dc exec -T db mysql -u app -papp-password course -e "$1" 2>&1; }
mi_php() { mi_dc run --rm --no-deps -T php php "$@" 2>&1; }
mi_drop="2026_11_27_1000_drop_phone_from_users.sql"
rm -f "$ROOT/migrations/migrations/$mi_drop"
out="$(mi_php migrate.php)"
expect "the first run applies both, in order" "$out" "Applying 2026_11_01_0900_create_users
Applying 2026_11_08_1400_add_phone_to_users
Applied 2."
expect "the second run has nothing to do" "$(mi_php migrate.php)" "Nothing to do. The database is up to date."
expect "the migrations table lists both" "$(mi_q 'SELECT COUNT(*) FROM `migrations`;' | tail -1)" "2"
mi_q "INSERT INTO \`users\` (\`email\`, \`name\`, \`phone\`) VALUES ('alice@example.com', 'Alice', '555-0100');" >/dev/null
expect "users has a phone column" "$(mi_q 'SELECT `phone` FROM `users`;')" "555-0100"
cp "$ROOT/migrations/later/$mi_drop" "$ROOT/migrations/migrations/"
out="$(mi_php migrate.php)"
rm -f "$ROOT/migrations/migrations/$mi_drop"
expect "the undo is a new migration, and only it runs" "$out" "Applying 2026_11_27_1000_drop_phone_from_users
Applied 1."
out="$(mi_q 'DESCRIBE `users`;')"
if grep -qF "phone" <<< "$out"; then fail "  ...and the phone column is gone" "$out"; else pass "  ...and the phone column is gone"; fi
expect "  ...Alice is still there" "$(mi_q 'SELECT `email` FROM `users`;')" "alice@example.com"
expect "the migrations table lists all three" "$(mi_q 'SELECT COUNT(*) FROM `migrations`;' | tail -1)" "3"
mi_dc down -v >/dev/null 2>&1
