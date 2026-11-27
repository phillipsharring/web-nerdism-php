# operators: https://phillipharrington.com/php/operators
out="$(cli operators math.php)"
expect "/ can give a float" "$out" "9 5 14 3.5"
expect "intdiv and %" "$out" "3 remainder 1"
expect "parentheses change the order" "$out" "7 but 9"
expect "+= and *=" "$out" "30"
expect ".=" "$out" "Hello, Alice"
out="$(cli operators compare.php)"
if [ "$(head -2 <<< "$out" | tr '\n' ' ')" = "bool(true) bool(false) " ]; then pass "== juggles, === doesn't"; else fail "== juggles, === doesn't" "$out"; fi
expect "?? only skips missing or null" "$out" 'string(0) ""'
expect "?: skips anything false-ish" "$out" 'string(5) "Guest"'
expect "the ternary" "$out" "items"
if [ "$(tail -2 <<< "$out" | tr '\n' ' ')" = "bool(false) bool(true) " ]; then pass "or binds looser than ="; else fail "or binds looser than =" "$out"; fi
serve operators
out="$(curl -s "http://localhost:$PORT/code.php?code=123456")"
expect "a wrong code is refused by ==" "$out" "==  says: refused"
out="$(curl -s "http://localhost:$PORT/code.php?code=0")"
expect "but 0 gets past ==" "$out" "==  says: let in"
expect "  ...and not past ===" "$out" "=== says: refused"
expect "the real code gets past ===" "$(curl -s "http://localhost:$PORT/code.php?code=0e462097431906509019562988736854")" "=== says: let in"
expect "an array instead of a code is refused" "$(curl -s -g "http://localhost:$PORT/code.php?code[]=0")" "=== says: refused"
stop
