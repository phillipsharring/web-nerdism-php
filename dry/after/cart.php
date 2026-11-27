<?php

declare(strict_types=1);

require __DIR__ . '/pricing.php';

$subtotalCents = 4500;

echo 'Cart total:    ', number_format(orderTotalCents($subtotalCents) / 100, 2), "\n";
