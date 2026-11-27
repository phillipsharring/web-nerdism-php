# deleting-data: https://phillipharrington.com/php/deleting-data
de_dc() { docker compose -p "$NAME-deleting-data" -f "$ROOT/deleting-data/compose.yml" "$@"; }
de_dc build -q php >/dev/null 2>&1
de_dc up -d --wait db >/dev/null 2>&1
de_q() { de_dc exec -T db mysql -u app -papp-password course "$@" 2>&1; }
de_php() { de_dc run --rm --no-deps -T php php "$@" 2>&1; }
out="$(de_php delete-post.php 1)"
expect "delete-post.php counts one post" "$out" "Posts deleted: 1"
expect "  ...and the cascade took its three comments" "$out" "Comments left: 1"
expect "  ...and refuses an id that isn't a number" "$(de_php delete-post.php '1 OR 1')" "Usage: php delete-post.php"
expect "RESTRICT refuses to delete a user with posts" "$(de_q -e 'DELETE FROM `users` WHERE `id` = 1;')" "Cannot delete or update a parent row"
out="$(de_q -e 'START TRANSACTION; DELETE FROM `comments`; SELECT COUNT(*) AS `during` FROM `comments`; ROLLBACK; SELECT COUNT(*) AS `after` FROM `comments`;')"
expect "a DELETE without a WHERE empties the table" "$(sed -n '/during/{n;p;}' <<< "$out")" "0"
expect "  ...until the ROLLBACK" "$(sed -n '/after/{n;p;}' <<< "$out")" "1"
expect "--safe-updates refuses it" "$(de_q --safe-updates -e 'DELETE FROM `comments`;')" "You are using safe update mode"
out="$(de_php close-account.php 2)"
expect "close-account.php closes Bo's account" "$out" "Closed account #2."
expect "  ...and he's left out of the list" "$out" "Open accounts: Alice"
expect "  ...but his row is still there" "$(de_q -e 'SELECT COUNT(*) FROM `users`;' | tail -1)" "2"
expect "  ...and his post" "$(de_q -e 'SELECT `title` FROM `posts` WHERE `user_id` = 2;')" "Bo was here"
expect "  ...and closing it twice does nothing" "$(de_php close-account.php 2)" "No open account #2."
expect "the closed row still holds his email" "$(de_q -e "INSERT INTO \`users\` (\`email\`, \`name\`) VALUES ('bo@example.com', 'Bo');")" "Duplicate entry 'bo@example.com'"
expect "TRUNCATE is refused on a table others point at" "$(de_q -e 'TRUNCATE TABLE `posts`;')" "Cannot truncate a table referenced in a foreign key constraint"
de_dc down -v >/dev/null 2>&1
