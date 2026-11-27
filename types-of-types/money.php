<?php
declare(strict_types=1);

// Floats are binary fractions, and 0.1 has no exact binary form.
var_dump(0.1 + 0.2);
var_dump(0.1 + 0.2 === 0.3);

// Ten things at 10 cents each, added up as floats.
$total = 0.0;
for ($i = 0; $i < 10; $i++) {
    $total += 0.10;
}
var_dump($total, $total === 1.0);

// The same, counted in cents, as ints: exact.
$cents = 0;
for ($i = 0; $i < 10; $i++) {
    $cents += 10;
}
var_dump($cents, $cents === 100);

// Splitting $100.00 three ways: cents make you decide where the extra penny goes.
$bill = 10000;
$share = intdiv($bill, 3);
$leftover = $bill % 3;
echo 'Each pays ', $share, ' cents, and someone pays ', $leftover, " more\n";

// Dollars only at the very end, for people to read.
echo '$', number_format($bill / 100, 2), "\n";
