# scope: https://phillipharrington.com/php/scope
out="$(cli scope scope.php)"
expect "a function can't see a global" "$out" 'welcome() cannot see $site'
expect "  ...so pass it in" "$out" "Welcome to Example Shop"
expect "a function's variables stay inside it" "$out" "Visits stayed inside count_visit()"
expect "superglobals are visible everywhere" "$out" "Running scope.php"
expect "a loop is not a scope" "$out" 'After the loop: $size is large, $last is large'
expect "closures take copies" "$out" "Hi, Alice / Hi, Bob"
out="$(cli scope reference.php)"
expect "a leftover reference overwrites the last item" "$out" "[2] => 400"
expect "  ...with the one before it" "$out" "[1] => 400"
