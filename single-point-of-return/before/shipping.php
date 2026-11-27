<?php

declare(strict_types=1);

// Returns from wherever it happens to know the answer. Every quote is meant to be logged.
function shippingCents(string $country, int $weightGrams): int
{
    if ($weightGrams < 0) {
        throw new InvalidArgumentException("weightGrams can't be negative, got $weightGrams");
    }
    if ($weightGrams === 0) {
        return 0;
    }

    if (isset(RATES_CENTS[$country])) {
        if ($weightGrams < HEAVY_FROM_GRAMS) {
            return RATES_CENTS[$country];
        }
        $cents = RATES_CENTS[$country] + HEAVY_SURCHARGE_CENTS;
        logQuote($country, $weightGrams, $cents);
        return $cents;
    }

    $cents = INTERNATIONAL_CENTS;
    logQuote($country, $weightGrams, $cents);
    return $cents;
}
