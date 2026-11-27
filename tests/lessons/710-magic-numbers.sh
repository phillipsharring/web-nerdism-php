# magic-numbers: https://phillipharrington.com/php/magic-numbers
before="$(cli magic-numbers before.php)"
after="$(cli magic-numbers after.php)"
expect "before works out an order" "$before" "#101: paid, shipping 5.99, tax 3.05, returns until 2026-12-20"
expect "  ...and calls an unknown status paid" "$before" "#104: paid"
expect "after gives the same answers" "$after" "#101: paid, shipping 5.99, tax 3.05, returns until 2026-12-20"
expect "  ...free shipping over the line" "$after" "#102: shipped, shipping 0.00, tax 4.64, returns until 2026-12-02"
expect "  ...pending waits" "$after" "#103: waiting for payment"
expect "  ...and refuses a status it doesn't know" "$after" "#104: unknown status 9, skipped"
