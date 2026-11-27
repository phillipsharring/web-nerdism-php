# single-point-of-return: https://phillipharrington.com/php/single-point-of-return
before="$(cli single-point-of-return run.php before)"
after="$(cli single-point-of-return run.php after)"
for line in "US, 800g: 5.99" "US, 2500g: 15.99" "CA, 800g: 14.99" "FR, 800g: 29.99" "US, 0g: 0.00"; do
    expect "before: $line" "$before" "$line"
    expect "  ...and after agrees" "$after" "$line"
done
if [ "$(grep -c 'logged:' <<< "$before")" = 2 ]; then pass "before logs only two of four quotes"; else fail "before logs only two of four quotes" "$before"; fi
if [ "$(grep -c 'logged:' <<< "$after")" = 4 ]; then pass "after logs all four"; else fail "after logs all four" "$after"; fi
expect "  ...including the one before missed" "$after" "logged: 1499 for CA, 800g"
