# classes-and-objects: https://phillipharrington.com/php/classes-and-objects
out="$(cli classes-and-objects shop.php)"
expect "each object has its own items" "$out" "Mine:  3 items, USD 32.50"
expect "  ...yours too" "$out" "Yours: 1 item, USD 12.00"
expect "two variables, one object" "$out" "after adding a hat to \$alsoMine: USD 52.50"
expect "clone makes a copy" "$out" "after adding socks to the copy:  USD 52.50"
expect "private means private" "$(cli classes-and-objects peek.php)" "Cannot access private property Cart::\$items"
expect "the cart keeps its own rules" "$(cli classes-and-objects -r 'require "Cart.php"; (new Cart("USD"))->add("Mug", 1200, 0);')" "Quantity must be at least 1"
