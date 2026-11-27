<?php

declare(strict_types=1);

// Trusts whoever calls it.
function discountedCents(int $priceCents, int $percentOff): int
{
    return $priceCents - intdiv($priceCents * $percentOff, 100);
}
