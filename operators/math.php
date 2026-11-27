<?php
declare(strict_types=1);

echo 7 + 2, ' ', 7 - 2, ' ', 7 * 2, ' ', 7 / 2, "\n"; // / can give a float: 3.5
echo intdiv(7, 2), ' remainder ', 7 % 2, "\n";           // whole-number division
echo 2 ** 10, "\n";                                     // to the power of
echo 1 + 2 * 3, ' but ', (1 + 2) * 3, "\n";             // * before +, unless you say otherwise

echo 'Hello, ' . 'world', "\n";                         // . joins strings; + doesn't

$total = 10;
$total += 5;  // the same as $total = $total + 5
$total *= 2;
echo $total, "\n";

$greeting = 'Hello';
$greeting .= ', Alice';
echo $greeting, "\n";

$count = 0;
$count++;
echo $count, "\n";
