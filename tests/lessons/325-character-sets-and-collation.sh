# character-sets-and-collation: https://phillipharrington.com/php/character-sets-and-collation
cs_dc() { docker compose -p "$NAME-character-sets" -f "$ROOT/character-sets-and-collation/compose.yml" "$@"; }
cs_dc build -q php >/dev/null 2>&1
cs_dc up -d --wait db >/dev/null 2>&1
cs_q() { cs_dc exec -T db mysql -u app -papp-password course -e "$1" 2>&1; }
cs_php() { cs_dc run --rm --no-deps -T php php "$@" 2>&1; }
out="$(cs_php save.php 'Hello 🐘')"
expect "utf8 refuses an emoji" "$out" "notes_utf8: refused."
expect "  ...and utf8mb4 saves it" "$out" "notes: saved"
expect "  ...intact" "$(cs_q 'SELECT `body` FROM `notes` WHERE `id` = 1;')" "Hello 🐘"
out="$(cs_php save.php 'café')"
expect "both take café" "$out" "notes_utf8: saved"
expect "save.php refuses an empty note" "$(cs_php save.php "")" "Usage: php save.php"
expect "utf8 is really utf8mb3" "$(cs_q 'SHOW CREATE TABLE `notes_utf8`;')" "CHARSET=utf8mb3"
out="$(cs_php wrong-charset.php)"
expect "the wrong connection reads back what it wrote" "$out" "Read back the wrong way: café"
expect "  ...but a right one finds mojibake" "$out" "Read back the right way: cafÃ©"
expect "  ...and the bytes were converted twice" "$out" "636166C383C2A9"
expect "ai_ci: cafe = café, Cafe = cafe" "$(cs_q "SELECT 'cafe' = 'café', 'Cafe' = 'cafe';" | tail -1)" "1	1"
expect "as_cs: cafe isn't café" "$(cs_q "SELECT 'cafe' = 'café' COLLATE utf8mb4_0900_as_cs;" | tail -1)" "0"
expect "the default sorts like a person" "$(cs_q 'SELECT `word` FROM `words` ORDER BY `word`;' | tail -5 | tr '\n' ' ')" "apple éclair Émile zebra Zoë"
expect "utf8mb4_bin sorts by bytes" "$(cs_q 'SELECT `word` FROM `words` ORDER BY `word` COLLATE utf8mb4_bin;' | tail -5 | tr '\n' ' ')" "Zoë apple zebra Émile éclair"
cs_q "INSERT INTO \`users\` (\`username\`) VALUES ('Cafe');" >/dev/null
expect "a unique column thinks café is Cafe" "$(cs_q "INSERT INTO \`users\` (\`username\`) VALUES ('café');")" "Duplicate entry 'café'"
expect "old collations think all emoji are equal" "$(cs_q "SELECT _utf8mb4'🐘' COLLATE utf8mb4_general_ci = _utf8mb4'🐬' COLLATE utf8mb4_general_ci AS \`same\`, _utf8mb4'🐘' = _utf8mb4'🐬' AS \`same_0900\`;" | tail -1)" "1	0"
cs_dc down -v >/dev/null 2>&1
