<?php

declare(strict_types=1);

// The one place that knows how shipping works.

const FREE_SHIPPING_FROM_CENTS = 4000;
const SHIPPING_CENTS = 599;

function shippingCents(int $subtotalCents): int
{
    return $subtotalCents >= FREE_SHIPPING_FROM_CENTS ? 0 : SHIPPING_CENTS;
}

function orderTotalCents(int $subtotalCents): int
{
    return $subtotalCents + shippingCents($subtotalCents);
}
