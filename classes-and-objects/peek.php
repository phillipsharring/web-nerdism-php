<?php
declare(strict_types=1);

require __DIR__ . '/Cart.php';

$cart = new Cart('USD');
$cart->add('T-shirt', 1500);

// From outside the class, the private items are off limits.
echo count($cart->items), "\n";
