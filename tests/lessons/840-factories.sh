# factories: https://phillipharrington.com/php/factories
withenv() { local f="$1"; shift; docker run --rm "$@" -v "$ROOT/factories":/app -w /app "$IMAGE" php "$f" 2>&1; }
SMTP=(-e SMTP_HOST=smtp.example.com -e SMTP_PORT=587)
expect "before: log works" "$(withenv before.php -e MAILER=log)" "[log] To alice@example.com: Welcome!"
expect "  ...smtp works" "$(withenv before.php -e MAILER=smtp "${SMTP[@]}")" "[smtp smtp.example.com:587]"
expect "  ...a typo quietly logs instead of sending" "$(withenv before.php -e MAILER=smpt "${SMTP[@]}")" "[log] To alice@example.com"
expect "  ...a bad port quietly becomes 0" "$(withenv before.php -e MAILER=smtp -e SMTP_HOST=smtp.example.com -e SMTP_PORT=abc)" "[smtp smtp.example.com:0]"
expect "after: log works" "$(withenv after.php -e MAILER=log)" "[log] To alice@example.com: Welcome!"
expect "  ...smtp works" "$(withenv after.php -e MAILER=smtp "${SMTP[@]}")" "[smtp smtp.example.com:587] To alice@example.com: Welcome!"
if [ -z "$(withenv after.php -e MAILER=none)" ]; then pass "  ...none sends nothing, and says nothing"; else fail "  ...none sends nothing, and says nothing" "$(withenv after.php -e MAILER=none)"; fi
expect "  ...a typo stops the app" "$(withenv after.php -e MAILER=smpt "${SMTP[@]}")" "MAILER must be log, none or smtp, got 'smpt'"
expect "  ...a bad port stops it too" "$(withenv after.php -e MAILER=smtp -e SMTP_HOST=smtp.example.com -e SMTP_PORT=abc)" "SMTP_PORT must be 1 to 65535, got 'abc'"
expect "  ...and so does a missing host" "$(withenv after.php -e MAILER=smtp -e SMTP_PORT=587)" "SMTP_HOST is required for MAILER=smtp"
