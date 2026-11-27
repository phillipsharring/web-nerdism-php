# dependencies: https://phillipharrington.com/php/dependencies
# Works in a temp copy: the Try It creates composer.json and the lock itself,
# with a vulnerable version, and none of that is ever committed.
deps="$(mktemp -d)"
cp "$ROOT/dependencies/version.php" "$deps/"
composer() { docker run --rm -v "$deps":/app -w /app composer:2 composer "$@" 2>&1; }
composer_unguarded() { docker run --rm -e COMPOSER_NO_SECURITY_BLOCKING=1 -v "$deps":/app -w /app composer:2 composer "$@" 2>&1; }
out="$(composer require guzzlehttp/guzzle:7.4.0 --no-interaction; echo "exit $?")"
expect "Composer refuses a version with known advisories" "$out" "could not be resolved to an installable set of packages"
out="$(composer_unguarded require guzzlehttp/guzzle:7.4.0 --no-interaction)"
expect "with the guard off, it installs 7.4.0" "$out" "Locking guzzlehttp/guzzle (7.4.0)"
expect "  ...and says why it shouldn't" "$out" "security vulnerability advisories affecting 1 package"
out="$(docker run --rm -v "$deps":/app -w /app "$IMAGE" php version.php 2>&1)"
expect "version.php reports Guzzle 7.4.0" "$out" "Guzzle 7.4.0"
expect "  ...which is old enough to complain on PHP 8.4" "$out" "Implicitly marking parameter"
out="$(composer audit --format=plain; echo "exit $?")"
expect "composer audit finds advisories" "$out" "security vulnerability advisories affecting 1 package"
expect "  ...including a 2022 one" "$out" "CVE-2022-29248"
expect "  ...and exits with an error" "$out" "exit 1"
out="$(composer require "guzzlehttp/guzzle:^7.4" --with-all-dependencies --no-interaction)"
expect "requiring ^7.4 with its dependencies upgrades it" "$out" "Upgrading guzzlehttp/guzzle (7.4.0 => 7."
out="$(composer audit; echo "exit $?")"
expect "then composer audit is clean" "$out" "No security vulnerability advisories found."
expect "  ...and exits 0" "$out" "exit 0"
out="$(docker run --rm -v "$deps":/app -w /app "$IMAGE" php version.php 2>&1)"
expect "version.php reports the new version" "$out" "Guzzle 7."
if grep -qF "Deprecated" <<< "$out"; then fail "  ...without the complaints" "$out"; else pass "  ...without the complaints"; fi
docker run --rm -v "$deps":/x "$IMAGE" rm -rf /x/vendor
rm -rf "$deps"
