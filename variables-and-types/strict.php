<?php
declare(strict_types=1);

// With strict_types, an int means an int. No converting.
function double(int $number): int
{
    return $number * 2;
}

var_dump(double(21));
var_dump(double('21')); // a TypeError, right here
