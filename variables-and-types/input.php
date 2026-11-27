<?php
declare(strict_types=1);

function double(int $number): int
{
    return $number * 2;
}

// What you type after the file name arrives as a string, like every form field.
// Never trust input: check it, then convert it on purpose.
$input = $argv[1] ?? '';

echo 'A cast takes what it can: ', (int) $input, "\n";

$number = filter_var($input, FILTER_VALIDATE_INT); // a whole number, or false

if ($number === false) {
    echo 'Not a whole number: ', $input, "\n";
} else {
    echo 'Doubled: ', double($number), "\n";
}
