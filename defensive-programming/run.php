<?php

declare(strict_types=1);

// php run.php before, or php run.php after. Even an argument is input: only two are allowed.
$version = $argv[1] ?? '';
if (!in_array($version, ['before', 'after'], true)) {
    fwrite(STDERR, "Usage: php run.php before|after\n");
    exit(1);
}

require __DIR__ . "/$version/discount.php";

foreach ([25, 150, -50] as $percentOff) {
    try {
        $price = number_format(discountedCents(2000, $percentOff) / 100, 2);
    } catch (InvalidArgumentException $e) {
        $price = 'refused: ' . $e->getMessage();
    }
    echo "$percentOff% off 20.00: $price\n";
}

// The types are a defense too. With strict_types, a string isn't an int, even '25'.
try {
    discountedCents(2000, '25');
} catch (TypeError $e) {
    echo "'25' as a string: refused by the type\n";
}
