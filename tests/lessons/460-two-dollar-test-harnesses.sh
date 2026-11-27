# two-dollar-test-harnesses: https://phillipharrington.com/php/two-dollar-test-harnesses
out="$(cli two-dollar-test-harnesses harness.php)"
expect "the harness makes a link" "$out" "https://example.com/verify?email=ada%40example.com&expires=1800003600&sig="
for check in "a fresh link is good" "a changed email is refused" "an expired link is refused" "another secret is refused" "junk is refused"; do
    expect "  ok: $check" "$out" "ok    $check"
done
if grep -qF "FAIL" <<< "$out"; then fail "  ...and nothing fails" "$out"; else pass "  ...and nothing fails"; fi
docker run --rm -v "$ROOT/two-dollar-test-harnesses":/app -w /app php:8.4-cli php harness.php >/dev/null 2>&1
expect "  ...so it exits 0" "exit $?" "exit 0"
# Break the expiry check, the way the lesson says to, in a copy.
broken="$(mktemp -d)"
cp -R "$ROOT/two-dollar-test-harnesses/." "$broken/"
sed -i.bak 's/if ((int) $expires < $now) {/if (false) {/' "$broken/src/verification.php"
out="$(docker run --rm -v "$broken":/app -w /app php:8.4-cli php harness.php 2>&1)"
code=$?
expect "a broken expiry check is caught" "$out" "FAIL  an expired link is refused"
expect "  ...and the harness exits 1" "exit $code" "exit 1"
rm -rf "$broken"
