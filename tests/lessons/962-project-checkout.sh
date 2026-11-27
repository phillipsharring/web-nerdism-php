# project-checkout: https://phillipharrington.com/php/projects/checkout
co() { APP_PORT=$PORT docker compose -p "$NAME-checkout" -f "$ROOT/project-checkout/compose.yml" "$@"; }
sql() { co exec -T db mysql -uapp -papp-password -N course -e "$1" 2>/dev/null; }
dump() { co exec -T db mysqldump --no-tablespaces -uapp -papp-password course 2>/dev/null; }
rm -f "$ROOT/project-checkout/logs/app.log"
docker run --rm -v "$ROOT/project-checkout":/app -w /app composer:2 composer install -q --no-interaction 2>&1
co up -d --build --wait >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
U="http://localhost:$PORT"
J="$(mktemp)"
c() { curl -s -c "$J" -b "$J" "$@"; }
csrf="$(c "$U/" | sed -n 's/.*name="csrf" value="\([a-f0-9]*\)".*/\1/p' | head -1)"
c -o /dev/null -d "csrf=$csrf" -d action=add -d product=1 -d quantity=2 "$U/cart.php"
c -o /dev/null -d "csrf=$csrf" -d action=add -d product=6 -d quantity=1 "$U/cart.php"
page="$(c "$U/checkout.php")"
expect "the checkout shows the total" "$page" "Total: \$32.99"
if grep -qiE 'name="card|cvc|cc-number|cc-csc' <<< "$page"; then fail "  ...and has no card fields at all" "$page"; else pass "  ...and has no card fields at all"; fi
checkout="$(sed -n 's/.*name="checkout" value="\([a-f0-9]*\)".*/\1/p' <<< "$page")"
place() { c -o /dev/null -w '%{redirect_url}' -d "csrf=$csrf" -d "checkout=$checkout" -d email=ann@example.com -d name=Ann "$@" "$U/checkout.php"; }
first="$(place -d total_cents=1 -d card_number=4242424242424242 -d card_cvc=123)"
expect "placing the order redirects to it" "$first" "/order.php?id="
expect "  ...with the total from the database, not the form" "$(sql "SELECT total_cents, status FROM orders")" "3299	awaiting_payment"
expect "  ...names and prices copied into the order" "$(sql "SELECT name, unit_cents, quantity FROM order_items ORDER BY name")" "Black mug	1400	2"
expect "  ...stock taken" "$(sql "SELECT stock FROM products WHERE id = 1")" "23"
expect "  ...and the cart emptied" "$(c "$U/cart.php")" "Your cart is empty."
expect "pressing Place order again gives the same order" "$(place)" "$first"
expect "  ...and still only one order" "$(sql "SELECT COUNT(*) FROM orders")" "1"
order="$(c "$U${first#http://localhost:$PORT}")"
expect "the order page" "$order" "Thanks, Ann"
expect "  ...shows the status" "$order" "Status: awaiting payment"
expect "someone else's browser gets a 404 for it" "$(curl -si "$first")" "HTTP/1.1 404"
if dump | grep -qF 4242424242424242; then fail "the card number sent anyway is nowhere in the database" ""; else pass "the card number sent anyway is nowhere in the database"; fi
if [ -f "$ROOT/project-checkout/logs/app.log" ]; then fail "  ...or in a log" "$(cat "$ROOT/project-checkout/logs/app.log")"; else pass "  ...or in a log"; fi
# Not enough stock
c -o /dev/null -d "csrf=$csrf" -d action=add -d product=3 -d quantity=8 "$U/cart.php"
page="$(c "$U/checkout.php")"; checkout="$(sed -n 's/.*name="checkout" value="\([a-f0-9]*\)".*/\1/p' <<< "$page")"
sql "UPDATE products SET stock = 2 WHERE id = 3"
expect "if the stock ran out meanwhile, the order is refused" "$(c -d "csrf=$csrf" -d "checkout=$checkout" -d email=ann@example.com -d name=Ann "$U/checkout.php")" "Only 2 Big mug left."
expect "  ...and nothing is taken" "$(sql "SELECT stock FROM products WHERE id = 3"), $(sql "SELECT COUNT(*) FROM orders")" "2, 1"
expect "a bad email is refused" "$(c -d "csrf=$csrf" -d "checkout=$checkout" -d email=nope -d name=Ann "$U/checkout.php")" "An email address, please"
expect "a forged checkout token is refused" "$(c -d "csrf=$csrf" -d "checkout=$(printf '%064d' 0)" -d email=ann@example.com -d name=Ann "$U/checkout.php")" "That form expired."
# Break It: the naive checkout
curl -s -o /dev/null -d email=bo@example.com -d card_number=4242424242424242 -d card_expiry=12/34 -d card_cvc=123 -d total_cents=2800 "$U/naive-checkout.php"
expect "naive: the card number is in the log" "$(cat "$ROOT/project-checkout/logs/app.log" 2>&1)" '"card_number":"4242424242424242","card_expiry":"12\/34","card_cvc":"123"'
expect "naive: and in the database, CVC and all" "$(dump | grep -F naive_orders | grep -F INSERT)" "'4242424242424242','12/34','123'"
rm -f "$J" "$ROOT/project-checkout/logs/app.log"
co down -v >/dev/null 2>&1
