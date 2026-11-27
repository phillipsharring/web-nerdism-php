# modern-php: https://phillipharrington.com/php/modern-php
out="$(cli modern-php order.php paid)"
expect "an enum from input, with match for the label" "$out" "Order 1042: paid, not shipped yet, coupon: none"
expect "the nullsafe operator reaches a coupon that's there" "$out" "Order 1043: paid, not shipped yet, coupon: FALL10"
expect "a backed enum stores as a string" "$out" "Saved as: paid"
expect "readonly means readonly" "$out" "Cannot modify readonly property Order::\$totalCents"
expect "another status, another label" "$(cli modern-php order.php shipped)" "Order 1042: on its way"
expect "tryFrom() turns away a status that isn't one" "$(cli modern-php order.php lost)" "Unknown status. Use one of: pending, paid, shipped"
expect "from() throws instead" "$(cli modern-php -r 'enum S: string { case A = "a"; } S::from("lost");')" '"lost" is not a valid backing value for enum S'
expect "match with no match throws" "$(cli modern-php -r 'echo match (3) { 1 => "one", 2 => "two" };')" "UnhandledMatchError: Unhandled match case 3"
