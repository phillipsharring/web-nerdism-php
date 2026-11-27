# output: https://phillipharrington.com/php/output
out="$(cli output dump.php)"
expect "echo" "$out" "echo prints strings: 1999"
expect "printf fills in the pattern" "$out" "printf fills in a pattern: A shirt costs 1999 cents"
expect "sprintf returns a string" "$out" "sprintf made: 00042"
expect "echo false prints nothing" "$out" "echo false prints: []"
expect "print_r" "$out" "[1] => web"
expect "var_dump shows types" "$out" "bool(false)"
expect "var_export writes PHP" "$out" "1 => 'web',"
serve output
expect "a name from the query string" "$(curl -s "http://localhost:$PORT/?name=Alice")" "Hello, Alice."
out="$(curl -s --get --data-urlencode 'name=<b>Alice</b>' "http://localhost:$PORT/")"
expect "HTML in the name is escaped" "$out" "Hello, &lt;b&gt;Alice&lt;/b&gt;."
if grep -qF "<b>Alice</b>" <<< "$out"; then fail "  ...never raw" "$out"; else pass "  ...never raw"; fi
expect "an array instead of a name is refused" "$(curl -s -g "http://localhost:$PORT/?name[]=x")" "Hello, world."
out="$(curl -si "http://localhost:$PORT/too-late.php")"
expect "a header after output: headers already sent" "$out" "Cannot modify header information - headers already sent"
if grep -qi "^Location:" <<< "$out"; then fail "  ...and no redirect" "$out"; else pass "  ...and no redirect"; fi
stop
