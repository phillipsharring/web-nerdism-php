# strings: https://phillipharrington.com/php/strings
out="$(cli strings strings.php)"
expect "single quotes are literal" "$out" 'Hello, $name\n'
expect "double quotes fill in variables" "$out" "Hello, Alice"
expect "braces mark the variable" "$out" "You have 3 items"
expect "heredoc, with the indent taken off" "$out" "
Order for Alice"
expect "trim and strtolower" "$out" "[alice@example.com]"
expect "str_replace" "$out" "alice@example.org"
expect "explode" "$out" "[1] => example.com"
expect "implode" "$out" "red, green, blue"
expect "sprintf pads and rounds" "$out" "[Shirt   ] [ 19.50] [007]"
expect "number_format" "$out" "1,234,567.89"
