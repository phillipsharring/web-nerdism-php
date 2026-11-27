# magic-methods: https://phillipharrington.com/php/magic-methods
out="$(cli magic-methods magic.php)"
expect "__toString makes an object a string" "$out" 'Price: $19.99'
expect "__get reads a column" "$out" "Row email: ada@example.com"
expect "  ...and a typo is quietly nothing" "$out" "Row typo: []"
expect "a real property warns about the typo" "$out" "Warning: Undefined property: User::\$emial"
expect "__invoke makes an object callable" "$out" "Hello, Grace."
expect "  ...even by array_map()" "$out" "Hello, Ada. Hello, Katherine."
expect "__debugInfo hides the key" "$out" "[apiKey] => (hidden)"
if grep -qF "not-a-real-key" <<< "$out"; then fail "  ...completely" "$out"; else pass "  ...completely"; fi
