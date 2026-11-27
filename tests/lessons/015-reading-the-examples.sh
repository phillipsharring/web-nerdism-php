# reading-the-examples: https://phillipharrington.com/php/reading-the-examples
out="$(cli reading-the-examples cheat-sheet.php)"
expect "double quotes fill in the variable" "$out" "Hello, Alice"
expect "single quotes print it as written" "$out" 'Hello, $name'
expect "arrays count from 0" "$out" "small"
expect "a key finds its value" "$out" "blue"
expect "?? falls back when a key is missing" "$out" "no price"
expect "a function call" "$out" "ALICE"
expect "if runs when the test is true" "$out" "More than two"
expect "foreach runs once per item" "$out" "- large"
if grep -q "Warning\|Deprecated\|Error" <<< "$out"; then fail "runs clean" "$out"; else pass "runs clean"; fi
