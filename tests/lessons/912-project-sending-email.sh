# project-sending-email: https://phillipharrington.com/php/projects/sending-email
docker run --rm -v "$ROOT/project-sending-email":/app -w /app composer:2 composer install --no-interaction --quiet 2>&1
mp() { MAILPIT_PORT=$PORT docker compose -p "$NAME-sending-email" -f "$ROOT/project-sending-email/compose.yml" "$@"; }
mp up -d mailpit >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/api/v1/messages" && break; sleep 0.5; done
expect "send.php sends" "$(mp run --rm php php send.php alice@example.com 2>&1)" "Sent to alice@example.com."
msgs="$(curl -s "http://localhost:$PORT/api/v1/messages")"
expect "Mailpit caught it" "$msgs" '"Address":"alice@example.com"'
expect "  ...with the UTF-8 subject intact" "$msgs" '"Subject":"Welcome to the café"'
expect "  ...from the site's own address" "$msgs" '"From":{"Name":"Example Site","Address":"hello@example.com"}'
raw="$(curl -s "http://localhost:$PORT/api/v1/message/latest/raw")"
expect "  ...with a plain text part" "$raw" "Content-Type: text/plain; charset=utf-8"
expect "  ...and an HTML part" "$raw" "Content-Type: text/html; charset=utf-8"
expect "  ...and the subject encoded for the trip" "$raw" "Subject: Welcome to the =?utf-8?Q?caf=C3=A9?="
out="$(mp run --rm php php send.php $'alice@example.com\nBcc: everyone@example.com' 2>&1)"
expect "an address with a header in it is refused" "$out" "Usage: php send.php"
expect "  ...and nothing more was sent" "$(curl -s "http://localhost:$PORT/api/v1/messages")" '"total":1,'
mp down -v >/dev/null 2>&1
