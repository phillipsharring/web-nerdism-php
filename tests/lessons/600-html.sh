# html: https://phillipharrington.com/php/html
serve html
out="$(curl -s "http://localhost:$PORT/")"
expect "a page starts with the doctype" "$out" "<!doctype html>"
expect "  ...says its language" "$out" '<html lang="en">'
expect "  ...and its character set" "$out" '<meta charset="utf-8">'
expect "  ...and has a title" "$out" "<title>Shirts</title>"
expect "PHP builds the list" "$out" "<li>Medium</li>"
if grep -qF "Hello," <<< "$out"; then fail "no name, no greeting" "$out"; else pass "no name, no greeting"; fi
expect "a name is greeted" "$(curl -s "http://localhost:$PORT/?name=Alice")" "<p>Hello, Alice.</p>"
out="$(curl -s "http://localhost:$PORT/?name=%3Cem%3EAlice%3C/em%3E")"
expect "HTML in the name comes out as text" "$out" "Hello, &lt;em&gt;Alice&lt;/em&gt;."
if grep -qF "<em>Alice" <<< "$out"; then fail "  ...never as tags" "$out"; else pass "  ...never as tags"; fi
expect "an array where a string belongs is ignored" "$(curl -s -g "http://localhost:$PORT/?name[]=x" -o /dev/null -w '%{http_code}')" "200"
stop
