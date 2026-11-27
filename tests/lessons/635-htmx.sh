# htmx: https://phillipharrington.com/php/htmx
serve htmx -t public
out="$(curl -s "http://localhost:$PORT/")"
expect "the page loads htmx, pinned to a version" "$out" 'src="https://unpkg.com/htmx.org@2.0.4/dist/htmx.min.js"'
expect "  ...checked by its hash" "$out" 'integrity="sha384-'
expect "the input asks as you type" "$out" 'hx-get="/" hx-trigger="input changed delay:300ms" hx-target="#results"'
out="$(curl -si -H 'HX-Request: true' "http://localhost:$PORT/?q=b")"
expect "htmx gets only the list" "$out" "<li>Black</li>"
if grep -qF "<html" <<< "$out"; then fail "  ...not the whole page" "$out"; else pass "  ...not the whole page"; fi
expect "  ...and caches are told" "$out" "Vary: HX-Request"
out="$(curl -s "http://localhost:$PORT/?q=b")"
expect "without htmx, the whole page, results included" "$out" "<li>Brown</li>"
expect "  ...and the search in the box" "$out" 'value="b"'
out="$(curl -s "http://localhost:$PORT/?q=%22%3E%3Cscript%3E")"
expect "a search with HTML in it is escaped in the box" "$out" 'value="&quot;&gt;&lt;script&gt;"'
if grep -qF '"><script>' <<< "$out"; then fail "  ...never raw" "$out"; else pass "  ...never raw"; fi
stop
