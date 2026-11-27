<?php

declare(strict_types=1);

const RATES_CENTS = ['US' => 599, 'CA' => 1499];
const INTERNATIONAL_CENTS = 2999;
const HEAVY_FROM_GRAMS = 2000;
const HEAVY_SURCHARGE_CENTS = 1000;

// A real one would write to a log file. This one prints, so you can see it.
function logQuote(string $country, int $weightGrams, int $cents): void
{
    echo "    logged: $cents for $country, {$weightGrams}g\n";
}
