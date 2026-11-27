# selecting-data: https://phillipharrington.com/php/selecting-data
se_dc() { docker compose -p "$NAME-selecting-data" -f "$ROOT/selecting-data/compose.yml" "$@"; }
se_dc build -q php >/dev/null 2>&1
se_dc up -d --wait db >/dev/null 2>&1
se_q() { se_dc exec -T db mysql -u app -papp-password course -e "$1" 2>&1; }
se_php() { se_dc run --rm --no-deps -T php php "$@" 2>&1; }
expect "LEFT JOIN keeps the author with no books" "$(se_q 'SELECT `authors`.`name`, `books`.`title` FROM `authors` LEFT JOIN `books` ON `books`.`author_id` = `authors`.`id` ORDER BY `authors`.`name`;')" "Toni Morrison	NULL"
out="$(se_q 'SELECT `authors`.`name`, `books`.`title` FROM `authors` INNER JOIN `books` ON `books`.`author_id` = `authors`.`id` ORDER BY `authors`.`name`;')"
if grep -qF "Toni Morrison" <<< "$out"; then fail "INNER JOIN drops her" "$out"; else pass "INNER JOIN drops her"; fi
expect "= NULL finds nothing" "$(se_q 'SELECT COUNT(*) FROM `books` WHERE `rating` = NULL;' | tail -1)" "0"
expect "IS NULL finds both" "$(se_q 'SELECT COUNT(*) FROM `books` WHERE `rating` IS NULL;' | tail -1)" "2"
expect "<> 5 skips the NULLs: two books, not four" "$(se_q 'SELECT COUNT(*) FROM `books` WHERE `rating` <> 5;' | tail -1)" "2"
out="$(se_q 'SELECT `authors`.`name`, COUNT(*) AS `rows`, COUNT(`books`.`id`) AS `books` FROM `authors` LEFT JOIN `books` ON `books`.`author_id` = `authors`.`id` GROUP BY `authors`.`id` ORDER BY `authors`.`name`;')"
expect "COUNT(*) gives her 1, COUNT(column) gives her 0" "$out" "Toni Morrison	1	0"
expect "'2abc' finds row 2" "$(se_q "SELECT \`title\` FROM \`books\` WHERE \`id\` = '2abc';")" "Kindred"
expect "NULL sorts first" "$(se_q 'SELECT `rating` FROM `books` ORDER BY `rating`;' | grep -v -e Warning -e rating | head -1)" "NULL"
out="$(se_php list.php 1)"
expect "list.php page 1" "$out" "A Wizard of Earthsea, by Ursula K. Le Guin (4/5)"
out="$(se_php list.php 2)"
expect "list.php page 2" "$out" "Parable of the Sower, by Octavia E. Butler (not rated)"
if grep -qF "Frankenstein" <<< "$out"; then fail "  ...without page 1's books" "$out"; else pass "  ...without page 1's books"; fi
expect "a page that isn't a number is page 1" "$(se_php list.php '2 OR 1')" "Page 1"
out="$(se_php n-plus-one.php)"
expect "the N+1 way takes 5 queries" "$out" "The N+1 way: 5 queries"
expect "  ...the JOIN way, 1" "$out" "The JOIN way: 1 query"
if [ "$(grep -c 'Toni Morrison: (none)' <<< "$out")" -eq 2 ] && [ "$(grep -c 'Ursula K. Le Guin: A Wizard of Earthsea, The Left Hand of Darkness, The Dispossessed' <<< "$out")" -eq 2 ]; then pass "  ...and both print the same list"; else fail "  ...and both print the same list" "$out"; fi
se_dc down -v >/dev/null 2>&1
