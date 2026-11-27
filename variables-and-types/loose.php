<?php
// No strict_types: PHP converts what it can to fit the declared type.
function double(int $number): int
{
    return $number * 2;
}

var_dump(double(21));
var_dump(double('21'));  // the string becomes 21
var_dump(double(true));  // true becomes 1
var_dump(double('twenty-one')); // nothing to convert: a TypeError
