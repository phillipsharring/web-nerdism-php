<?php

declare(strict_types=1);

// Free shipping now starts at 40.00. Someone updated this page.
$subtotalCents = 4500;
$shippingCents = $subtotalCents >= 4000 ? 0 : 599;

echo 'Cart total:    ', number_format(($subtotalCents + $shippingCents) / 100, 2), "\n";
