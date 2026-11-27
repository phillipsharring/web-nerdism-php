# testing: https://phillipharrington.com/php/testing
docker run --rm -v "$ROOT/testing":/app -w /app composer:2 composer install -q 2>&1
out="$(cli testing vendor/bin/phpunit --colors=never)"
expect "every test passes" "$out" "OK (13 tests, 15 assertions)"
expect "--filter runs just one" "$(cli testing vendor/bin/phpunit --colors=never --filter Expired)" "OK (1 test, 1 assertion)"
expect "--testdox reads like a list" "$(cli testing vendor/bin/phpunit --colors=never --testdox)" "A tampered link is refused with data set \"changed email\""
# Break the expiry check, the way the lesson says to, in a copy.
broken="$(mktemp -d)"
cp -R "$ROOT/testing/." "$broken/"
sed -i.bak 's/if ((int) $expires < $now) {/if (false) {/' "$broken/src/verification.php"
out="$(docker run --rm -v "$broken":/app -w /app php:8.4-cli vendor/bin/phpunit --colors=never 2>&1)"
code=$?
expect "a broken expiry check fails a test" "$out" "Tests\\VerificationTest::testAnExpiredLinkIsRefused"
expect "  ...says what came back instead" "$out" "Failed asserting that 'ada@example.com' is null."
expect "  ...and exits non-zero" "exit $code" "exit 1"
rm -rf "$broken"
