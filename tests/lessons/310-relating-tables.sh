# relating-tables: https://phillipharrington.com/php/relating-tables
rt_dc() { docker compose -p "$NAME-relating-tables" -f "$ROOT/relating-tables/compose.yml" "$@"; }
rt_dc build -q php >/dev/null 2>&1
rt_dc up -d --wait db >/dev/null 2>&1
rt_q() { rt_dc exec -T db mysql -u app -papp-password course -e "$1" 2>&1; }
rt_php() { rt_dc run --rm --no-deps -T php php "$@" 2>&1; }
out="$(rt_q 'SELECT `books`.`title`, `authors`.`name` FROM `books` INNER JOIN `authors` ON `authors`.`id` = `books`.`author_id` ORDER BY `books`.`year`;')"
expect "INNER JOIN pairs each book with its author" "$out" "Kindred	Octavia E. Butler"
out="$(rt_q "SELECT \`books\`.\`title\` FROM \`books\` INNER JOIN \`book_tags\` ON \`book_tags\`.\`book_id\` = \`books\`.\`id\` INNER JOIN \`tags\` ON \`tags\`.\`id\` = \`book_tags\`.\`tag_id\` WHERE \`tags\`.\`name\` = 'science fiction' ORDER BY \`books\`.\`title\`;")"
expect "the many-to-many finds the science fiction" "$out" "Parable of the Sower"
if grep -qF "Earthsea" <<< "$out"; then fail "  ...and not the fantasy" "$out"; else pass "  ...and not the fantasy"; fi
expect "a book with no such author is refused" "$(rt_q "INSERT INTO \`books\` (\`author_id\`, \`title\`, \`year\`) VALUES (99, 'Nobody', 2000);")" "a foreign key constraint fails"
expect "an author with books can't be deleted" "$(rt_q 'DELETE FROM `authors` WHERE `id` = 1;')" "Cannot delete or update a parent row"
expect "the same pairing can't be added twice" "$(rt_q 'INSERT INTO `book_tags` (`book_id`, `tag_id`) VALUES (1, 1);')" "Duplicate entry '1-1'"
expect "mismatched key types are refused" "$(rt_q 'CREATE TABLE `reviews` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `book_id` INT NOT NULL, PRIMARY KEY (`id`), FOREIGN KEY (`book_id`) REFERENCES `books` (`id`));')" "are incompatible"
expect "add-book.php adds a book for a real author" "$(rt_php add-book.php 2 'Dawn' 1987)" "Added book #"
expect "  ...and the foreign key refuses author 99" "$(rt_php add-book.php 99 'Nobody' 2000)" "There's no author #99."
expect "  ...and the script refuses an id that isn't a number" "$(rt_php add-book.php '2 OR 1' 'Dawn' 1987)" "Usage: php add-book.php"
rt_dc down -v >/dev/null 2>&1
