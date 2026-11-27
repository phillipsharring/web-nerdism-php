# project-stripe-checkout: https://phillipharrington.com/php/projects/stripe-checkout
# Runs without any Stripe key. If STRIPE_SECRET_KEY is a test key (sk_test_...),
# it also creates a real test-mode Checkout Session.
WH=whsec_local_test_only
sk() { APP_PORT=$PORT STRIPE_WEBHOOK_SECRET=$WH STRIPE_SECRET_KEY="${KEY-}" docker compose -p "$NAME-stripe" -f "$ROOT/project-stripe-checkout/compose.yml" "$@"; }
sql() { sk exec -T db mysql -uapp -papp-password -N course -e "$1" 2>/dev/null; }
docker run --rm -v "$ROOT/project-stripe-checkout":/app -w /app composer:2 composer install -q --no-interaction 2>&1
KEY=""
sk up -d --build --wait >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
U="http://localhost:$PORT"
J="$(mktemp)"
c() { curl -s -c "$J" -b "$J" "$@"; }
csrf="$(c "$U/" | sed -n 's/.*name="csrf" value="\([a-f0-9]*\)".*/\1/p' | head -1)"
c -o /dev/null -d "csrf=$csrf" -d action=add -d product=1 -d quantity=2 "$U/cart.php"
checkout="$(c "$U/checkout.php" | sed -n 's/.*name="checkout" value="\([a-f0-9]*\)".*/\1/p')"
order_url="$(c -o /dev/null -w '%{redirect_url}' -d "csrf=$csrf" -d "checkout=$checkout" -d email=ann@example.com -d name=Ann "$U/checkout.php")"
oid="${order_url##*id=}"
expect "the order page has a Pay button" "$(c "$order_url")" 'Pay $28.00 with Stripe'
expect "with no key, paying refuses" "$(c -si -d "csrf=$csrf" -d "order=$oid" "$U/pay.php")" "HTTP/1.1 503"
expect "coming back from Stripe proves nothing" "$(c "$order_url&paid=cs_test_made_up")" "We're confirming your payment"
expect "  ...the order is still unpaid" "$(sql "SELECT status FROM orders")" "awaiting_payment"

# Webhooks, signed the way Stripe signs them.
event() { printf '{"id":"evt_test_1","object":"event","type":"%s","data":{"object":{"id":"cs_test_1","object":"checkout.session","client_reference_id":"%s","payment_status":"%s","amount_total":%s,"currency":"usd"}}}' "$@"; }
sign() { sk exec -T app php -r 'echo hash_hmac("sha256", $argv[1] . "." . $argv[2], $argv[3]);' "$1" "$2" "$3"; }
hook() { curl -s -o /dev/null -w '%{http_code}' -H "Stripe-Signature: $1" --data-binary "$2" "$U/webhook.php"; }
now="$(date +%s)"
good="$(event checkout.session.completed "$oid" paid 2800)"
expect "an unsigned webhook is refused" "$(curl -s -o /dev/null -w '%{http_code}' --data-binary "$good" "$U/webhook.php")" "400"
expect "a webhook signed with the wrong secret is refused" "$(hook "t=$now,v1=$(sign "$now" "$good" whsec_wrong)" "$good")" "400"
expect "  ...and the order is still unpaid" "$(sql "SELECT status FROM orders")" "awaiting_payment"
cheap="$(event checkout.session.completed "$oid" paid 1)"
expect "a signed event for the wrong amount is accepted" "$(hook "t=$now,v1=$(sign "$now" "$cheap" $WH)" "$cheap")" "200"
expect "  ...but doesn't mark it paid" "$(sql "SELECT status FROM orders")" "awaiting_payment"
unpaid="$(event checkout.session.completed "$oid" unpaid 2800)"
hook "t=$now,v1=$(sign "$now" "$unpaid" $WH)" "$unpaid" >/dev/null
expect "a completed session that isn't paid yet doesn't either" "$(sql "SELECT status FROM orders")" "awaiting_payment"
old=$((now - 600))
expect "a real signature from ten minutes ago is refused" "$(hook "t=$old,v1=$(sign "$old" "$good" $WH)" "$good")" "400"
expect "the right event, signed with the secret: 200" "$(hook "t=$now,v1=$(sign "$now" "$good" $WH)" "$good")" "200"
expect "  ...and the order is paid" "$(sql "SELECT status, paid_at IS NOT NULL FROM orders")" "paid	1"
expect "  ...and says so" "$(c "$order_url")" "Paid. Thank you!"
expect "the same event again is harmless" "$(hook "t=$now,v1=$(sign "$now" "$good" $WH)" "$good"), $(sql "SELECT COUNT(*) FROM orders WHERE status = 'paid'")" "200, 1"
KEY="sk_""live_""not-a-real-key"; sk up -d --wait app >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
expect "with a live key, paying refuses too" "$(curl -si -d "order=$oid" "$U/pay.php")" "this example never uses a live one"
sk down -v >/dev/null 2>&1

# A real test-mode Checkout Session, only when a test key is in the environment.
if [[ "${STRIPE_SECRET_KEY:-}" == sk_test_* ]]; then
    KEY="$STRIPE_SECRET_KEY"; sk up -d --wait >/dev/null 2>&1
    for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
    : > "$J"
    csrf="$(c "$U/" | sed -n 's/.*name="csrf" value="\([a-f0-9]*\)".*/\1/p' | head -1)"
    c -o /dev/null -d "csrf=$csrf" -d action=add -d product=1 "$U/cart.php"
    checkout="$(c "$U/checkout.php" | sed -n 's/.*name="checkout" value="\([a-f0-9]*\)".*/\1/p')"
    order_url="$(c -o /dev/null -w '%{redirect_url}' -d "csrf=$csrf" -d "checkout=$checkout" -d email=ann@example.com -d name=Ann "$U/checkout.php")"
    expect "with a test key, paying goes to Stripe's page" "$(c -o /dev/null -w '%{redirect_url}' -d "csrf=$csrf" -d "order=${order_url##*id=}" "$U/pay.php")" "https://checkout.stripe.com/"
    expect "  ...and the session is saved on the order" "$(sql "SELECT stripe_session_id FROM orders")" "cs_test_"
    sk down -v >/dev/null 2>&1
else
    pass "(no sk_test_ key in STRIPE_SECRET_KEY: skipped creating a real Checkout Session)"
fi
rm -f "$J"
