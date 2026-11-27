<?php
declare(strict_types=1);

// A loop variable outlives its loop. With a reference (&), that bites.
$prices = [100, 200, 300];

foreach ($prices as &$price) {
    $price = $price * 2; // & lets the loop change the array itself
}
// $price is still a reference to the last item, $prices[2].

foreach ($prices as $price) {
    // Each pass assigns to $price, which is $prices[2].
}

print_r($prices); // 200, 400, 400: the last item was overwritten

// The fix: unset($price) right after the first loop.
