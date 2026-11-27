# project-shopping-cart: https://phillipharrington.com/php/projects/shopping-cart
sc() { APP_PORT=$PORT docker compose -p "$NAME-cart" -f "$ROOT/project-shopping-cart/compose.yml" "$@"; }
docker run --rm -v "$ROOT/project-shopping-cart":/app -w /app composer:2 composer install -q --no-interaction 2>&1
sc up -d --build --wait >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
U="http://localhost:$PORT"
J="$(mktemp)"
c() { curl -s -c "$J" -b "$J" "$@"; }
expect "PHPUnit: the Cart tests pass" "$(sc run --rm -T app vendor/bin/phpunit tests 2>&1)" "OK (6 tests"
token="$(c "$U/" | sed -n 's/.*name="csrf" value="\([a-f0-9]*\)".*/\1/p' | head -1)"
expect "the shop has add-to-cart forms, with a token" "${#token}" "64"
add() { c -o /dev/null -w '%{http_code}' -d "csrf=$token" -d action=add "$@" "$U/cart.php"; }
expect "adding answers with a redirect" "$(add -d product=1 -d quantity=2)" "303"
out="$(c "$U/cart.php")"
expect "the cart has the line" "$out" "<td>Black mug</td>"
expect "  ...and the total, from the database" "$out" '<th>$28.00</th>'
expect "  ...and isn't cached" "$(c -I "$U/cart.php")" "Cache-Control: private, no-store"
add -d product=1 -d quantity=1 -d price_cents=1 -d 'price=0.01' >/dev/null
expect "a price in the form is ignored" "$(c "$U/cart.php")" '<th>$42.00</th>'
add -d product=6 -d quantity=-5 >/dev/null
if grep -qF "Sticker pack" <<< "$(c "$U/cart.php")"; then fail "a negative quantity adds nothing" ""; else pass "a negative quantity adds nothing"; fi
add -d product=3 -d quantity=50 >/dev/null
expect "more than the stock is cut to the stock" "$(c "$U/cart.php")" "Big mug (only 8 left)"
add -d product=7 >/dev/null
if grep -qF "Old sticker" <<< "$(c "$U/cart.php")"; then fail "an inactive product never shows up" ""; else pass "an inactive product never shows up"; fi
add -d product=2 >/dev/null
if grep -qF "White mug" <<< "$(c "$U/cart.php")"; then fail "a sold-out product never shows up" ""; else pass "a sold-out product never shows up"; fi
add -d 'product=1 OR 1=1' >/dev/null
expect "a product id that isn't a number changes nothing" "$(c "$U/cart.php")" '<th>$190.00</th>'
c -o /dev/null -d "csrf=$token" -d action=set -d product=3 -d quantity=1 "$U/cart.php"
expect "updating a quantity" "$(c "$U/cart.php")" '<th>$60.50</th>'
c -o /dev/null -d "csrf=$token" -d action=remove -d product=3 "$U/cart.php"
expect "removing a line" "$(c "$U/cart.php")" '<th>$42.00</th>'
expect "the shop shows the count" "$(c "$U/")" "Cart (3)"
expect "without the token, nothing changes" "$(curl -s -o /dev/null -w '%{http_code}' -b "$J" -d action=add -d product=6 "$U/cart.php")" "403"
expect "a new visitor has an empty cart" "$(curl -s "$U/cart.php")" "Your cart is empty."
rm -f "$J"
sc down -v >/dev/null 2>&1
