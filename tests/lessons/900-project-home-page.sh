# project-home-page: https://phillipharrington.com/php/projects/home-page
serve project-home-page -t public
out="$(curl -s "http://localhost:$PORT/")"
expect "the page has its heading" "$out" "<h1>Alex Example</h1>"
expect "  ...links from the array" "$out" '<a href="https://example.com/notes">Notes</a>'
expect "  ...this year in the footer" "$out" "&copy; $(date -u +%Y) Alex Example."
if grep -qE "Good (morning|afternoon|evening)\." <<< "$out"; then pass "  ...a greeting by the hour"; else fail "  ...a greeting by the hour" "$out"; fi
if grep -qF "<?" <<< "$out"; then fail "  ...and no PHP left in what's sent" "$out"; else pass "  ...and no PHP left in what's sent"; fi
expect "the stylesheet is served" "$(curl -si "http://localhost:$PORT/style.css")" "Content-Type: text/css"
stop
