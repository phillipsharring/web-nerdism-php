# output-buffering: https://phillipharrington.com/php/output-buffering
serve output-buffering -t public
out="$(curl -si "http://localhost:$PORT/too-late.php")"
expect "a header after output is refused" "$out" "Cannot modify header information - headers already sent"
if grep -qF "X-Greeting" <<< "$out"; then fail "  ...and never sent" "$out"; else pass "  ...and never sent"; fi
expect "with a buffer, the header makes it" "$(curl -si "http://localhost:$PORT/buffered.php")" "X-Greeting: hi"
out="$(curl -si "http://localhost:$PORT/?name=Ada")"
expect "the template is rendered into a string" "$out" "<p>Hello, Ada.</p>"
expect "  ...so its length is known first" "$out" "Content-Length: 79"
expect "no name, no problem" "$(curl -s "http://localhost:$PORT/")" "Hello, stranger."
out="$(curl -s --get --data-urlencode 'name=<b>Ada</b>' "http://localhost:$PORT/")"
expect "input is escaped in the template" "$out" "Hello, &lt;b&gt;Ada&lt;/b&gt;."
stop
