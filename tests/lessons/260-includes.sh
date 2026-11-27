# includes: https://phillipharrington.com/php/includes
serve includes -t public
out="$(curl -s "http://localhost:$PORT/")"
expect "the header came from its own file" "$out" "<title>Example Shop</title>"
expect "  ...the page" "$out" "<h1>Welcome to Example Shop</h1>"
expect "  ...and the footer, with config.php's values" "$out" "&copy; 2026 Example Shop"
expect "another page, same pieces" "$(curl -s "http://localhost:$PORT/?page=about")" "Same header, same footer, a different middle."
for attempt in '../config' '../../../../etc/passwd' 'home.php' 'HOME'; do
    out="$(curl -si --get --data-urlencode "page=$attempt" "http://localhost:$PORT/")"
    if grep -qF "404 Not Found" <<< "$out" && grep -qF "no page by that name" <<< "$out"; then
        pass "page=$attempt is refused"
    else
        fail "page=$attempt is refused" "$out"
    fi
done
for path in /config.php /functions.php /templates/header.php; do
    out="$(curl -s "http://localhost:$PORT$path")"
    if grep -qF "<?php" <<< "$out"; then fail "$path can't be reached by URL" "$out"; else pass "$path can't be reached by URL"; fi
done
stop
out="$(cli includes missing.php)"
expect "include of a missing file is a warning" "$out" "After include: still running"
expect "require of a missing file is fatal" "$out" "Failed opening required"
if grep -qF "you never see this" <<< "$out"; then fail "  ...and stops the script" "$out"; else pass "  ...and stops the script"; fi
