<?php

declare(strict_types=1);

$subtotalCents = 4500;
$shippingCents = $subtotalCents < 5000 ? 599 : 0;

echo 'Invoice total: ', number_format(($subtotalCents + $shippingCents) / 100, 2), "\n";
