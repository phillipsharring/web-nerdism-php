# secrets-out-of-code: https://phillipharrington.com/php/secrets-out-of-code
# Break It: a secret "removed" in a later commit is still in the history.
leak="$(mktemp -d)"
(
    cd "$leak" && git init -q && g() { git -c user.name=Example -c user.email=dev@example.com "$@"; }
    cp "$ROOT/secrets-out-of-code/naive-config.php" config.php
    git add config.php && g commit -qm "Add config"
    cp "$ROOT/secrets-out-of-code/config.php" config.php
    g commit -qam "Move the key out of the code"
)
expect "the latest commit has no key in it" "$(cd "$leak" && grep -c pretend-live-key config.php)" "0"
expect "  ...but the history does" "$(cd "$leak" && git log -p | grep pretend-live-key)" "-    'payment_api_key' => 'pretend-live-key-8f3a9c2e7d41',"
rm -rf "$leak"

out="$(cli secrets-out-of-code app.php)"
expect "with no environment, it stops" "$out" "PAYMENT_API_KEY is not set. See .env.example."
envfile="$(mktemp)"
printf 'PAYMENT_API_KEY=pretend-test-key-5b7e\nDB_PASSWORD=pretend-db-password\n' > "$envfile"
out="$(docker run --rm --env-file "$envfile" -v "$ROOT/secrets-out-of-code":/app -w /app "$IMAGE" php app.php 2>&1)"
expect "with --env-file, it loads the key" "$out" "Payment key loaded: ...5b7e"
if grep -qF "pretend-test-key" <<< "$out"; then fail "  ...and never prints it" "$out"; else pass "  ...and never prints it"; fi
printf 'PAYMENT_API_KEY="quoted-key"\nDB_PASSWORD=x\n' > "$envfile"
expect "--env-file keeps quotes as part of the value" "$(docker run --rm --env-file "$envfile" -v "$ROOT/secrets-out-of-code":/app -w /app "$IMAGE" php app.php 2>&1)" 'Payment key loaded: ...key"'
rm -f "$envfile"
expect "a missing second secret stops it too" "$(docker run --rm -e PAYMENT_API_KEY=abc1234 -v "$ROOT/secrets-out-of-code":/app -w /app "$IMAGE" php app.php 2>&1)" "DB_PASSWORD is not set."
expect ".env is ignored by git" "$(cd "$ROOT" && git check-ignore secrets-out-of-code/.env)" "secrets-out-of-code/.env"
