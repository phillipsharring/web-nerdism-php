# normalization: https://phillipharrington.com/php/normalization
no_dc() { docker compose -p "$NAME-normalization" -f "$ROOT/normalization/compose.yml" "$@"; }
no_dc build -q php >/dev/null 2>&1
no_dc up -d --wait db >/dev/null 2>&1
no_q() { no_dc exec -T db mysql -u app -papp-password course -e "$1" 2>&1; }
no_php() { no_dc run --rm --no-deps -T php php "$@" 2>&1; }
no_q "UPDATE \`orders_flat\` SET \`customer_email\` = 'alice@example.org' WHERE \`id\` = 3;" >/dev/null
out="$(no_q "SELECT DISTINCT \`customer_name\`, \`customer_email\` FROM \`orders_flat\` WHERE \`customer_name\` = 'Alice Rivera';")"
expect "flat: one update gives Alice two emails" "$out" "alice@example.com"
expect "  ...both of them" "$out" "alice@example.org"
no_q "UPDATE \`customers\` SET \`email\` = 'alice@example.org' WHERE \`id\` = 1;" >/dev/null
out="$(no_q 'SELECT `orders`.`id`, `customers`.`email` FROM `orders` INNER JOIN `customers` ON `customers`.`id` = `orders`.`customer_id` WHERE `customers`.`id` = 1;')"
if [ "$(grep -c 'alice@example.org' <<< "$out")" -eq 3 ]; then pass "normalized: one update, all three orders"; else fail "normalized: one update, all three orders" "$out"; fi
no_q 'UPDATE `products` SET `price_cents` = 1400 WHERE `id` = 1;' >/dev/null
expect "the order keeps the price paid" "$(no_q 'SELECT `orders`.`id`, `orders`.`price_cents` AS `paid`, `products`.`price_cents` AS `now` FROM `orders` INNER JOIN `products` ON `products`.`id` = `orders`.`product_id` WHERE `products`.`id` = 1;')" "1	1200	1400"
expect "place-order.php uses the current price" "$(no_php place-order.php 2 1)" "Order #5 placed, at 14.00"
out="$(no_q 'SELECT `customers`.`name`, `customers`.`order_count`, COUNT(`orders`.`id`) AS `counted` FROM `customers` LEFT JOIN `orders` ON `orders`.`customer_id` = `customers`.`id` GROUP BY `customers`.`id`;')"
expect "  ...and the copied count matches the real one" "$out" "Bo Chen	2	2"
expect "  ...for Alice too" "$out" "Alice Rivera	3	3"
expect "it refuses ids that aren't numbers" "$(no_php place-order.php 2 'x')" "Usage: php place-order.php"
expect "  ...and a product that isn't there" "$(no_php place-order.php 2 99)" "There's no product #99."
no_dc down -v >/dev/null 2>&1
