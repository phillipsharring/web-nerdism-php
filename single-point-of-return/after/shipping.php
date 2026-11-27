<?php

declare(strict_types=1);

function shippingCents(string $country, int $weightGrams): int
{
    // Guards first: they return or throw before any work starts.
    if ($weightGrams < 0) {
        throw new InvalidArgumentException("weightGrams can't be negative, got $weightGrams");
    }
    if ($weightGrams === 0) {
        return 0; // A download. Nothing to ship, nothing to quote.
    }

    // Then the work: one result, built up, and one way out.
    $cents = RATES_CENTS[$country] ?? INTERNATIONAL_CENTS;
    if (isset(RATES_CENTS[$country]) && $weightGrams >= HEAVY_FROM_GRAMS) {
        $cents += HEAVY_SURCHARGE_CENTS;
    }

    logQuote($country, $weightGrams, $cents);

    return $cents;
}
