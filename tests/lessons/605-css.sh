# css: https://phillipharrington.com/php/css
serve css -t public
out="$(curl -s "http://localhost:$PORT/")"
expect "the page links its stylesheet, with a version" "$out" '<link rel="stylesheet" href="/style.css?v='
v="$(grep -o 'style.css?v=[0-9]*' <<< "$out" | cut -d= -f2)"
if [ -n "$v" ]; then pass "  ...the file's change time ($v)"; else fail "  ...the file's change time" "$out"; fi
expect "the default theme is light" "$out" '<body class="light">'
expect "dark is on the list" "$(curl -s "http://localhost:$PORT/?theme=dark")" '<body class="dark">'
out="$(curl -s "http://localhost:$PORT/?theme=%22%3E%3Cscript%3E")"
expect "anything else falls back to light" "$out" '<body class="light">'
if grep -qF "<script>" <<< "$out"; then fail "  ...and never reaches the page" "$out"; else pass "  ...and never reaches the page"; fi
expect "the stylesheet is served as CSS" "$(curl -sI "http://localhost:$PORT/style.css?v=$v")" "Content-Type: text/css"
stop
