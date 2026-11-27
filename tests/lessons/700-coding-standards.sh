# coding-standards: https://phillipharrington.com/php/coding-standards
comp() { local dir="$1"; shift; docker run --rm -v "$ROOT/$dir":/app -w /app composer:2 composer "$@" 2>&1; }
comp coding-standards install -q >/dev/null
out="$(comp coding-standards check before.php)"
expect "the messy file fails PSR-12" "$out" "FOUND 68 ERRORS"
expect "  ...and phpcbf can fix all of it" "$out" "PHPCBF CAN FIX THE 68 MARKED SNIFF VIOLATIONS AUTOMATICALLY"
out="$(comp coding-standards check after.php)"
if grep -qF "ERROR" <<< "$out"; then fail "the clean file passes" "$out"; else pass "the clean file passes"; fi
cp "$ROOT/coding-standards/before.php" "$ROOT/coding-standards/fixed.php"
expect "the fixer fixes a copy" "$(comp coding-standards fix fixed.php)" "A TOTAL OF 68 ERRORS WERE FIXED IN 1 FILE"
out="$(comp coding-standards check fixed.php)"
if grep -qF "ERROR" <<< "$out"; then fail "  ...and the copy then passes" "$out"; else pass "  ...and the copy then passes"; fi
rm -f "$ROOT/coding-standards/fixed.php"
if [ "$(cli coding-standards before.php)" = "$(cli coding-standards after.php)" ]; then pass "both files print the same receipt"; else fail "both files print the same receipt" "$(cli coding-standards before.php)"; fi
expect "  ...which is right" "$(cli coding-standards after.php)" "Total: 44.00"
