# project-store-front: https://phillipharrington.com/php/projects/store-front
sf() { APP_PORT=$PORT docker compose -p "$NAME-store" -f "$ROOT/project-store-front/compose.yml" "$@"; }
sf up -d --build --wait >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
U="http://localhost:$PORT"
out="$(curl -s "$U/")"
expect "the list shows products" "$out" '<a href="/products/black-mug">Black mug</a>'
expect "  ...prices from whole cents" "$out" '$4.99'
expect "  ...sold out marked" "$out" "\$14.00 (sold out)"
if grep -qF "Old sticker" <<< "$out"; then fail "  ...inactive products hidden" "$out"; else pass "  ...inactive products hidden"; fi
out="$(curl -s "$U/?category=mugs&sort=price-high")"
expect "a category and a sort" "$(grep -o 'products/[a-z-]*' <<< "$out" | head -1)" "products/big-mug"
if grep -qF "Shirt" <<< "$(grep '<li>' -A1 <<< "$out")"; then fail "  ...only that category" "$out"; else pass "  ...only that category"; fi
expect "price, low first" "$(curl -s "$U/?sort=price-low" | grep -o 'products/[a-z-]*' | head -1)" "products/sticker-pack"
expect "an unknown sort falls back to name" "$(curl -s "$U/?sort=price_cents%3B%20DROP%20TABLE%20products" | grep -o 'products/[a-z-]*' | head -1)" "products/big-mug"
expect "  ...and the table is still there" "$(curl -s "$U/")" "Black mug"
expect "an unknown category shows everything" "$(curl -s "$U/?category=%27%20OR%201%3D1")" "Sticker pack"
expect "an array for a category is ignored" "$(curl -s "$U/?category[]=mugs")" "Sticker pack"
out="$(curl -s "$U/products/sticker-pack")"
expect "a product page" "$out" "<h1>Sticker pack</h1>"
expect "  ...UTF-8 intact" "$out" "Café-proof."
expect "  ...in stock" "$out" "In stock"
expect "a sold-out product says so" "$(curl -s "$U/products/white-mug")" "Sold out"
expect "an inactive product is a 404" "$(curl -si "$U/products/old-sticker")" "HTTP/1.1 404"
expect "an unknown product is a 404" "$(curl -si "$U/products/nope")" "HTTP/1.1 404"
expect "money(): thousands and cents" "$(sf exec -T app php -r 'require "src/money.php"; echo money(123456789), " ", money(5), " ", money(0);')" '$1,234,567.89 $0.05 $0.00'
sf down -v >/dev/null 2>&1
