<?php

declare(strict_types=1);

// Checks what it's handed, and refuses loudly, with the value it got.
function discountedCents(int $priceCents, int $percentOff): int
{
    if ($priceCents < 0) {
        throw new InvalidArgumentException("priceCents can't be negative, got $priceCents");
    }
    if ($percentOff < 0 || $percentOff > 100) {
        throw new InvalidArgumentException("percentOff must be 0 to 100, got $percentOff");
    }

    return $priceCents - intdiv($priceCents * $percentOff, 100);
}
