# mvc: https://phillipharrington.com/php/mvc
docker run --rm -v "$ROOT/mvc":/app -w /app composer:2 composer install -q 2>&1
serve mvc -t public
B="http://localhost:$PORT"
out="$(curl -s "$B/")"
expect "the list shows the whole menu" "$out" '<a href="/item.php?id=4">Soup of the day</a> 6.50'
expect "  ...in UTF-8" "$out" "Café au lait"
out="$(curl -s "$B/?category=drinks")"
expect "a category narrows it" "$out" "<h1>Menu: drinks</h1>"
if grep -qF "Croissant" <<< "$out"; then fail "  ...to drinks only" "$out"; else pass "  ...to drinks only"; fi
out="$(curl -s "$B/?category=%3Cb%3E")"
expect "a category that isn't ours is ignored" "$out" "<h1>Menu</h1>"
if grep -qF "<b>" <<< "$out"; then fail "  ...and never printed raw" "$out"; else pass "  ...and never printed raw"; fi
expect "  ...an array too" "$(curl -s "$B/?category[]=drinks")" "<h1>Menu</h1>"
expect "an item has its own page" "$(curl -s "$B/item.php?id=2")" "<p>drinks, 4.50</p>"
expect "an unknown item is a 404" "$(curl -s -o /dev/null -w '%{http_code}' "$B/item.php?id=99")" "404"
expect "  ...and so is one that isn't a number" "$(curl -s -w '%{http_code}' "$B/item.php?id=abc")" "That's not on the menu."
stop
