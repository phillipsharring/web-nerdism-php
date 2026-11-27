# dont-touch-cards: https://phillipharrington.com/php/dont-touch-cards
docker run --rm -v "$ROOT/dont-touch-cards":/app -w /app composer:2 composer install -q --no-progress >/dev/null 2>&1
cards_dc() { SHOP_PORT=$PORT docker compose -p "$NAME-dont-touch-cards" -f "$ROOT/dont-touch-cards/compose.yml" "$@"; }
cards_dc up -d >/dev/null 2>&1
# Wait for the shop, and for stripe-mock behind it.
for _ in $(seq 1 60); do
    [ "$(curl -s -o /dev/null -w '%{http_code}' -X POST "http://localhost:$PORT/checkout.php")" = 303 ] && break
    sleep 0.5
done

out="$(curl -s "http://localhost:$PORT/")"
expect "the shop page has a Buy button" "$out" 'action="/checkout.php"'
if grep -qiE 'card|cvc' <<< "$(grep -v '<!--\|-->' <<< "$out" | grep '<input')"; then fail "  ...and no card fields" "$out"; else pass "  ...and no card fields"; fi
out="$(curl -s -o /dev/null -w '%{http_code} %{redirect_url}' -X POST "http://localhost:$PORT/checkout.php")"
expect "checkout sends the visitor to Stripe with a 303" "$out" "303 https://checkout.stripe.com/"
expect "checkout only takes a POST" "$(curl -s -w ' %{http_code}' "http://localhost:$PORT/checkout.php")" "Use the button on the shop page. 405"
expect "visiting the thanks page doesn't make it paid" "$(curl -s "http://localhost:$PORT/thanks.php?session_id=cs_test_a1b2c3")" "We haven't received your payment yet."
expect "the thanks page refuses an id that isn't one" "$(curl -s -w ' %{http_code}' --get --data-urlencode 'session_id=../../v1/customers' "http://localhost:$PORT/thanks.php")" "No such order. 404"
if cards_dc logs shop 2>&1 | grep -qF 4242424242424242; then fail "the fixed shop's log has no card numbers" "$(cards_dc logs shop 2>&1)"; else pass "the fixed shop's log has no card numbers"; fi

expect "naive: the card form takes a card" "$(curl -s -d 'card_number=4242424242424242&card_expiry=12/34&card_cvc=123' "http://localhost:$PORT/naive.php")" "Thanks! (Nothing was charged."
sleep 1
out="$(cards_dc logs shop 2>&1)"
expect "naive: now the card number is in your log" "$out" '"card_number":"4242424242424242"'
expect "  ...and the CVC, which may never be stored" "$out" '"card_cvc":"123"'
cards_dc down >/dev/null 2>&1
