# model-table-mapper: https://phillipharrington.com/php/model-table-mapper
docker run --rm -v "$ROOT/model-table-mapper":/app -w /app composer:2 composer install -q 2>&1
mtm() { docker compose -p "$NAME-model-table-mapper" -f "$ROOT/model-table-mapper/compose.yml" "$@"; }
out="$(mtm run --rm --build php 2>/dev/null)"
expect "a Note goes in as a row and comes back a Note" "$out" "Saved #1: Order more oat milk ☕, at 9:00 am"
expect "  ...UTF-8 intact both ways" "$out" "Now: Order oat milk today ☕"
expect "the model refuses a blank note" "$out" "Refused: A note is 1 to 500 characters, got 0"
expect "a good row is listed" "$out" "Listed #1: Order oat milk today ☕"
expect "  ...a bad row from the database is skipped" "$out" "Skipped row #2: A note is 1 to 500 characters, got 0"
expect "delete removes the row" "$out" "After delete: 1 row left"
mtm down -v >/dev/null 2>&1
