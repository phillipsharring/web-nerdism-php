# naming-things: https://phillipharrington.com/php/naming-things
before="$(cli naming-things before.php)"
expect "before saves and announces one post" "$before" 'Emailing subscribers: new post "Hello"'
expect "  ...refuses a taken slug" "$before" "Refused: that slug is taken."
expect "  ...refuses a blank title" "$before" "Refused: a post needs a title."
expect "  ...refuses someone who isn't the author" "$before" "Refused: only the author can save this post."
after="$(cli naming-things after.php)"
if [ "$before" = "$after" ]; then pass "after, split and named for what each piece is, does exactly the same"; else fail "after, split and named for what each piece is, does exactly the same" "$after"; fi
expect "class and function names ignore case" "$(cli naming-things -r 'class Invoice {} function totalCents() { return 1; } var_dump(new invoice() instanceof INVOICE, TOTALCENTS());')" "bool(true)
int(1)"
expect "variable names don't" "$(cli naming-things -r '$total = 1; var_dump(isset($Total));')" "bool(false)"
