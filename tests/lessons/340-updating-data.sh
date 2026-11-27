# updating-data: https://phillipharrington.com/php/updating-data
up_dc() { docker compose -p "$NAME-updating-data" -f "$ROOT/updating-data/compose.yml" "$@"; }
up_dc build -q php >/dev/null 2>&1
up_dc up -d --wait db >/dev/null 2>&1
up_q() { up_dc exec -T db mysql -u app -papp-password course "$@" 2>&1; }
up_php() { up_dc run --rm --no-deps -T php php "$@" 2>&1; }
expect "set-price.php changes a price" "$(up_php set-price.php 2 1800)" "Product #2 now costs 18.00."
expect "  ...the same price again: matched, not changed" "$(up_php set-price.php 2 1800)" "Product #2 already costs 18.00. Nothing changed."
expect "  ...a product that isn't there" "$(up_php set-price.php 99 100)" "There's no product #99."
expect "  ...and an id that isn't a number" "$(up_php set-price.php '2 OR 1' 100)" "Usage: php set-price.php"
expect "the mysql command says matched 1, changed 0" "$(up_q -v -v -e 'UPDATE `products` SET `price_cents` = 1800 WHERE `id` = 2;')" "Rows matched: 1  Changed: 0"
expect "sell.php sells the last T-shirt" "$(up_php sell.php 2)" "Sold one. 0 left."
expect "  ...and not another" "$(up_php sell.php 2)" "Sorry, product #2 is sold out."
expect "  ...and the stock stays at 0" "$(up_q -e 'SELECT `stock` FROM `products` WHERE `id` = 2;' | tail -1)" "0"
expect "--safe-updates refuses an UPDATE without a WHERE" "$(up_q --safe-updates -e 'UPDATE `products` SET `price_cents` = 0;')" "You are using safe update mode"
out="$(up_q -e 'START TRANSACTION; UPDATE `products` SET `price_cents` = 0; SELECT SUM(`price_cents`) AS `total` FROM `products`; ROLLBACK; SELECT SUM(`price_cents`) AS `after` FROM `products`;')"
expect "without a WHERE, everything is free" "$(sed -n '/total/{n;p;}' <<< "$out")" "0"
expect "  ...until the ROLLBACK" "$(sed -n '/after/{n;p;}' <<< "$out")" "4500"
expect "strict mode refuses '2abc' in an UPDATE" "$(up_q -e "UPDATE \`products\` SET \`stock\` = 5 WHERE \`id\` = '2abc';")" "Truncated incorrect DOUBLE value: '2abc'"
up_dc down -v >/dev/null 2>&1
