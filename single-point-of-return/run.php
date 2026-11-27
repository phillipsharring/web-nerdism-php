<?php

declare(strict_types=1);

// php run.php before, or php run.php after.
$version = $argv[1] ?? '';
if (!in_array($version, ['before', 'after'], true)) {
    fwrite(STDERR, "Usage: php run.php before|after\n");
    exit(1);
}

require __DIR__ . '/rates.php';
require __DIR__ . "/$version/shipping.php";

foreach ([['US', 800], ['US', 2500], ['CA', 800], ['FR', 800], ['US', 0]] as [$country, $grams]) {
    $price = number_format(shippingCents($country, $grams) / 100, 2);
    echo "$country, {$grams}g: $price\n";
}
