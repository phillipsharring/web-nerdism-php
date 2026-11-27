# naming-things: https://phillipharrington.com/php/naming-things
before="$(cli naming-things before.php)"
expect "before finds the free-shipping orders" "$before" "Free shipping: A-1001, A-1003"
after="$(cli naming-things after.php)"
if [ "$before" = "$after" ]; then pass "  ...and after, renamed, finds the same"; else fail "  ...and after, renamed, finds the same" "$after"; fi
expect "class and function names ignore case" "$(cli naming-things -r 'class Invoice {} function totalCents() { return 1; } var_dump(new invoice() instanceof INVOICE, TOTALCENTS());')" "bool(true)
int(1)"
expect "variable names don't" "$(cli naming-things -r '$total = 1; var_dump(isset($Total));')" "bool(false)"
expect "a reserved word can't be a class name" "$(cli naming-things -r 'class Int {}')" 'Cannot use "Int" as a class name as it is reserved'
