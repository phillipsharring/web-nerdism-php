# variables-and-types: https://phillipharrington.com/php/variables-and-types
out="$(cli variables-and-types variables.php)"
expect "var_dump shows each type" "$out" 'float(19.99)'
expect "a variable can change type" "$out" 'string(5) "three"'
expect "names are case-sensitive" "$out" "Alice and Bob"
expect "'5' + 5 is a number" "$out" "int(10)"
expect "'5' . 5 is a string" "$out" 'string(2) "55"'
out="$(cli variables-and-types loose.php)"
expect "loose: true is converted to 1" "$out" "int(2)"
if [ "$(grep -c "int(42)" <<< "$out")" -eq 2 ]; then pass "  ...and '21' to 21"; else fail "  ...and '21' to 21" "$out"; fi
expect "  ...but 'twenty-one' is a TypeError" "$out" "must be of type int, string given"
out="$(cli variables-and-types strict.php)"
expect "strict: 21 works" "$out" "int(42)"
expect "  ...and '21' is a TypeError" "$out" "double('21')"
expect "checked input is converted on purpose" "$(cli variables-and-types input.php 21)" "Doubled: 42"
out="$(cli variables-and-types input.php 21abc)"
expect "a cast takes what it can" "$out" "A cast takes what it can: 21"
expect "  ...filter_var refuses it" "$out" "Not a whole number: 21abc"
