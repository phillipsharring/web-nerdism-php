# project-redirecting: https://phillipharrington.com/php/projects/redirecting
serve project-redirecting -t public
out="$(curl -si "http://localhost:$PORT/about-us.php")"
expect "a moved page is a 301" "$out" "HTTP/1.1 301"
expect "  ...to its new address" "$out" "Location: /about.php"
out="$(curl -si -d 'theme=dark&back=/about.php' "http://localhost:$PORT/settings.php")"
expect "saving settings is a 303" "$out" "HTTP/1.1 303"
expect "  ...back to the page you came from" "$out" "Location: /about.php"
expect "  ...with the theme saved" "$out" "Set-Cookie: theme=dark;"
expect "the theme sticks" "$(curl -s -b 'theme=dark' "http://localhost:$PORT/about.php")" '<body class="dark">'
expect "a made-up theme is ignored" "$(curl -s -b 'theme="><script>' "http://localhost:$PORT/")" '<body class="light">'
expect "naive: back to anywhere at all" "$(curl -si -d 'theme=dark&back=https://evil.example/login' "http://localhost:$PORT/naive-settings.php")" "Location: https://evil.example/login"
expect "naive: a link can carry it in" "$(curl -s "http://localhost:$PORT/naive-settings.php?back=https://evil.example/login")" 'value="https://evil.example/login"'
for back in 'https://evil.example/login' '//evil.example/login' '/\evil.example/login' 'javascript:alert(1)' $'/\t/evil.example' 'evil.example'; do
    out="$(curl -si --data-urlencode "back=$back" -d 'theme=dark' "http://localhost:$PORT/settings.php")"
    if grep -q $'^Location: /\r$' <<< "$out"; then pass "fixed: back=$back goes home instead"; else fail "fixed: back=$back goes home instead" "$out"; fi
done
expect "fixed: a link can't carry it in" "$(curl -s "http://localhost:$PORT/settings.php?back=//evil.example/login")" 'name="back" value="/"'
expect "fixed: a path with a query string is fine" "$(curl -si --data-urlencode 'back=/about.php?x=1' "http://localhost:$PORT/settings.php")" "Location: /about.php?x=1"
stop
