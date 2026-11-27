# project-newsletter: https://phillipharrington.com/php/projects/newsletter
nl() { APP_PORT=$PORT MAIL_PORT=$((PORT + 1)) docker compose -p "$NAME-newsletter" -f "$ROOT/project-newsletter/compose.yml" "$@"; }
sql() { nl exec -T db mysql -uapp -papp-password -N course -e "$1" 2>/dev/null; }
M="http://localhost:$((PORT + 1))/api/v1"
# Reads Mailpit's API from inside the app container, where PHP can decode the JSON.
# mp to|text|header ADDRESS [HEADER]: how many emails to ADDRESS, or the latest one's text or header.
mp() { nl exec -T app php -r '
    [, $what, $to] = $argv; $api = "http://mailpit:8025/api/v1";
    $all = json_decode(file_get_contents("$api/messages?limit=500"), true)["messages"];
    $mine = array_values(array_filter($all, fn ($m) => $m["To"][0]["Address"] === $to));
    if ($what === "to") { echo count($mine); exit; }
    $id = $mine[0]["ID"] ?? exit;
    if ($what === "text") { echo json_decode(file_get_contents("$api/message/$id"), true)["Text"]; exit; }
    echo implode("", json_decode(file_get_contents("$api/message/$id/headers"), true)[$argv[3]] ?? []);
' "$@"; }
to() { mp to "$1"; }
latest_text() { mp text "$1"; }
docker run --rm -v "$ROOT/project-newsletter":/app -w /app composer:2 composer install -q --no-interaction 2>&1
nl up -d --build --wait >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
U="http://localhost:$PORT"

# Break It first: the naive form.
for i in $(seq 1 10); do curl -s -o /dev/null -d email=victim@example.com "$U/naive.php"; done
expect "naive: ten signups for a stranger, ten emails in their inbox" "$(to victim@example.com)" "10"
expect "naive: and the stranger is on the list" "$(sql "SELECT status FROM subscribers WHERE email = 'victim@example.com'")" "confirmed"
sql "DELETE FROM subscribers"; curl -s -X DELETE "$M/messages" >/dev/null

# Fix It: the real form.
for i in $(seq 1 3); do out="$(curl -s -d email=ann@example.com "$U/")"; done
expect "the form says thanks" "$out" "Check your inbox for a link to confirm."
expect "three signups for one address: one email" "$(to ann@example.com)" "1"
expect "  ...and the address is only pending" "$(sql "SELECT status FROM subscribers WHERE email = 'ann@example.com'")" "pending"
expect "  ...with only a hash of the token stored" "$(sql "SELECT LENGTH(confirm_token_hash) FROM subscribers WHERE email = 'ann@example.com'")" "64"
link="$(latest_text ann@example.com | grep -o 'http://localhost:[0-9]*/confirm.php?token=[a-f0-9]*' | head -1)"
expect "the email has a confirmation link" "$link" "/confirm.php?token="
expect "opening the link only shows a button" "$(curl -s "$link")" "Yes, send me the newsletter"
expect "  ...so a mail scanner opening it confirms nothing" "$(sql "SELECT status FROM subscribers WHERE email = 'ann@example.com'")" "pending"
token="${link##*token=}"
expect "pressing the button confirms" "$(curl -s -d "token=$token" "$U/confirm.php")" "You're subscribed."
expect "  ...once" "$(curl -si -d "token=$token" "$U/confirm.php")" "HTTP/1.1 400"
expect "a made-up token confirms nothing" "$(curl -si -d "token=$(printf '%064d' 0)" "$U/confirm.php")" "HTTP/1.1 400"
curl -s -o /dev/null -d email=ann@example.com "$U/"
expect "signing up again once confirmed sends nothing" "$(to ann@example.com)" "1"
curl -s -o /dev/null -d email=bo@example.com "$U/"
expect "the honeypot: a bot is thanked" "$(curl -s -d email=bot@example.com -d company=Acme "$U/")" "Check your inbox"
expect "  ...and sent nothing" "$(to bot@example.com)" "0"
expect "a bad address is refused" "$(curl -si -d 'email=nope' "$U/")" "HTTP/1.1 422"
for i in 1 2 3; do curl -s -o /dev/null -d "email=x$i@example.com" "$U/"; done
expect "the sixth signup from one visitor in an hour is a 429" "$(curl -si -d email=x9@example.com "$U/")" "HTTP/1.1 429"
expect "  ...and sends nothing" "$(to x9@example.com)" "0"

# Sending an issue.
expect "a dry run counts confirmed subscribers only" "$(nl exec -T app php bin/send.php issues/2026-11.txt --dry-run)" "to 1 subscriber(s)."
expect "sending" "$(nl exec -T app php bin/send.php issues/2026-11.txt)" 'Sent "The November issue" to 1 subscriber(s).'
expect "  ...the confirmed address gets it" "$(to ann@example.com)" "2"
expect "  ...the pending one doesn't" "$(to bo@example.com)" "1"
expect "  ...with a List-Unsubscribe header" "$(mp header ann@example.com List-Unsubscribe)" "<http://localhost:$PORT/unsubscribe.php?id="
expect "  ...and one-click" "$(mp header ann@example.com List-Unsubscribe-Post)" "List-Unsubscribe=One-Click"
unsub="$(latest_text ann@example.com | grep -o 'http://localhost:[0-9]*/unsubscribe.php?id=[0-9]*&sig=[a-f0-9]*' | head -1)"
expect "  ...and an unsubscribe link in the text" "$unsub" "unsubscribe.php?id="
expect "a forged unsubscribe link does nothing" "$(curl -si -X POST "$U/unsubscribe.php?id=1&sig=forged")" "HTTP/1.1 400"
expect "one-click unsubscribe, the way a mail program does it" "$(curl -s -d 'List-Unsubscribe=One-Click' "$unsub")" "You won't get the newsletter any more."
expect "  ...and the next issue goes to nobody" "$(nl exec -T app php bin/send.php issues/2026-11.txt)" "to 0 subscriber(s)."
nl down -v >/dev/null 2>&1
