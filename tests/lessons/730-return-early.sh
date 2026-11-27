# return-early: https://phillipharrington.com/php/return-early
before="$(cli return-early run.php before)"
for line in "Please log in." "Please verify your email first." "Your cart is empty." "Too many items: 20 at most." "Ready to check out 3 items."; do
    expect "before: $line" "$before" "$line"
done
after="$(cli return-early run.php after)"
if [ "$before" = "$after" ]; then pass "after gives the same five answers, in order"; else fail "after gives the same five answers, in order" "$after"; fi
