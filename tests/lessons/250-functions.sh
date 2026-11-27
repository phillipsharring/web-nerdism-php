# functions: https://phillipharrington.com/php/functions
out="$(cli functions functions.php)"
expect "a typed function" "$out" '$19.99'
expect "a default" "$out" "Hello, Alice!"
expect "  ...overridden" "$out" "Welcome back, Bob!"
expect "named arguments" "$out" "Hi, Carol!"
expect "checked input comes back as an int" "$out" "int(3)"
if [ "$(grep -c '^NULL$' <<< "$out")" -eq 3 ]; then pass "  ...and bad input as null, three ways"; else fail "  ...and bad input as null, three ways" "$out"; fi
expect "variadic" "$out" '$35.00'
expect "void" "$out" "[log] Order total worked out"
expect "a first-class callable" "$out" "1, 2, 3"
expect "strict_types refuses a string" "$out" "format_cents(): Argument #1 (\$cents) must be of type int, string given"
