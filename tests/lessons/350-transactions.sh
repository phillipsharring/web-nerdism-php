# transactions: https://phillipharrington.com/php/transactions
tx_dc() { docker compose -p "$NAME-transactions" -f "$ROOT/transactions/compose.yml" "$@"; }
tx_dc build -q php >/dev/null 2>&1
tx_dc up -d --wait db >/dev/null 2>&1
tx_q() { tx_dc exec -T db mysql -u app -papp-password course "$@" 2>&1; }
tx_php() { tx_dc run --rm --no-deps -T php php "$@" 2>&1; }
expect "the accounts start with 15000 between them" "$(tx_php balances.php)" "Total: 15000"
expect "the naive transfer to nobody says it worked" "$(tx_php transfer-naive.php 1 99 2500)" "Transferred 2500 cents from #1 to #99."
out="$(tx_php balances.php)"
expect "  ...and 2500 cents are gone" "$out" "Total: 12500"
expect "  ...taken from Alice" "$out" "#1 Alice: 7500"
expect "with a transaction, nothing is transferred" "$(tx_php transfer.php 1 99 2500)" "Nothing transferred. Account #99 doesn't exist."
out="$(tx_php balances.php)"
expect "  ...and Alice keeps her money" "$out" "#1 Alice: 7500"
expect "  ...and the total doesn't change" "$out" "Total: 12500"
expect "a good transfer works" "$(tx_php transfer.php 2 1 1000)" "Transferred 1000 cents from #2 to #1."
expect "  ...but not more than Bo has" "$(tx_php transfer.php 2 1 999999)" "doesn't have 999999 cents"
out="$(tx_php balances.php)"
expect "  ...so Bo has 4000" "$out" "#2 Bo: 4000"
expect "  ...and Alice 8500" "$out" "#1 Alice: 8500"
expect "a negative amount is refused" "$(tx_php transfer.php 2 1 -2500)" "Usage: php transfer.php"
out="$(tx_php isolation.php)"
expect "A sees its own change" "$out" "A, inside its transaction, sees: 8501"
expect "  ...B doesn't, yet" "$out" "B, outside it, sees:             8500"
expect "  ...until A commits" "$out" "B, after A commits, sees:        8501"
out="$(tx_q -e 'START TRANSACTION; UPDATE `accounts` SET `balance_cents` = 0 WHERE `id` = 1; CREATE TABLE `scratch` (`id` INT); ROLLBACK; SELECT `balance_cents` AS `after` FROM `accounts` WHERE `id` = 1;')"
expect "a CREATE TABLE commits the open transaction" "$(sed -n '/after/{n;p;}' <<< "$out")" "0"
tx_dc down -v >/dev/null 2>&1
