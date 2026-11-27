# project-contact-form: https://phillipharrington.com/php/projects/contact-form
docker run --rm -v "$ROOT/project-contact-form":/app -w /app composer:2 composer install --no-interaction --quiet 2>&1
rm -rf "$ROOT/project-contact-form/storage"
cf() { APP_PORT=$PORT MAILPIT_PORT=$((PORT + 1)) docker compose -p "$NAME-contact-form" -f "$ROOT/project-contact-form/compose.yml" "$@"; }
cf up -d >/dev/null 2>&1
app="http://localhost:$PORT"; mp="http://localhost:$((PORT + 1))/api/v1"
for _ in $(seq 1 30); do curl -s -o /dev/null "$app/" && curl -s -o /dev/null "$mp/messages" && break; sleep 0.5; done
total() { curl -s "$mp/messages" | grep -o '"total":[0-9]*' | head -1; }

# Naive: the spam cannon.
for i in 1 2 3 4 5; do
    curl -s -o /dev/null -d 'name=Prize&email=victim@example.com&copy=on&message=You won! https://spam.example' "$app/naive.php"
done
expect "naive: five posts send ten emails" "$(total)" '"total":10'
msgs="$(curl -s "$mp/messages")"
expect "  ...half of them to a stranger" "$msgs" '"To":[{"Name":"","Address":"victim@example.com"}]'
expect "  ...claiming to be from the stranger" "$msgs" '"From":{"Name":"","Address":"victim@example.com"}'
expect "naive: a bad address spills a stack trace" "$(curl -s -d 'name=x&email=nope&message=hi' "$app/naive.php")" "RfcComplianceException"
curl -s -X DELETE "$mp/messages" >/dev/null

# Fixed.
expect "fixed: a bad address is refused politely" "$(curl -s -d 'name=x&email=nope&message=hi' "$app/")" "An email address we can reply to, please."
expect "fixed: a missing message is refused" "$(curl -s -d 'name=x&email=a@example.com&message=' "$app/")" "A message, please"
expect "fixed: input comes back escaped" "$(curl -s --data-urlencode 'name=<b>x</b>' -d 'email=nope&message=hi' "$app/")" 'value="&lt;b&gt;x&lt;/b&gt;"'
expect "fixed: the honeypot pretends to succeed" "$(curl -si -d 'name=Bot&email=bot@example.com&message=hi&website=https://spam.example' "$app/")" "Location: /?sent=1"
expect "  ...and sends nothing" "$(total)" '"total":0'
for i in 1 2 3; do
    expect "fixed: message $i is sent" "$(curl -si -d 'name=Alice&email=alice@example.com&copy=on&message=Hello there' "$app/")" "HTTP/1.1 303"
done
out="$(curl -si -d 'name=Alice&email=alice@example.com&message=Hello again' "$app/")"
expect "fixed: the fourth in ten minutes is a 429" "$out" "HTTP/1.1 429"
expect "  ...with a polite note" "$out" "That&#039;s a lot of messages."
expect "only three emails went out, no copies" "$(total)" '"total":3'
msgs="$(curl -s "$mp/messages")"
expect "  ...to the owner" "$msgs" '"To":[{"Name":"","Address":"owner@example.com"}]'
expect "  ...from the site" "$msgs" '"From":{"Name":"Contact form","Address":"hello@example.com"}'
expect "  ...with the visitor in Reply-To" "$msgs" '"ReplyTo":[{"Name":"Alice","Address":"alice@example.com"}]'
if grep -qF 'victim@example.com' <<< "$msgs" || grep -qF '"Address":"alice@example.com"}]' <<< "$(grep -o '"To":\[[^]]*\]' <<< "$msgs")"; then fail "  ...and never to the visitor" "$msgs"; else pass "  ...and never to the visitor"; fi
cf down -v >/dev/null 2>&1
rm -rf "$ROOT/project-contact-form/storage"
