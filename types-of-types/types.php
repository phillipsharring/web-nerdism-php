<?php
declare(strict_types=1);

// The scalar types, plus null and array.
$values = [42, 3.14, 'hello', true, null, ['a', 'b']];
foreach ($values as $value) {
    echo str_pad(get_debug_type($value), 7), json_encode($value), "\n";
}

// Ints have a limit. Go past it, and you get a float.
var_dump(PHP_INT_MAX, PHP_INT_MAX + 1);

// Every one of these counts as false in an if.
$falsy = ['false' => false, '0' => 0, '0.0' => 0.0, "''" => '', "'0'" => '0', '[]' => [], 'null' => null];
foreach ($falsy as $label => $value) {
    echo $label, ' is ', $value ? 'true' : 'false', "\n";
}
echo "'false' is ", 'false' ? 'true' : 'false', ", because it's a string with something in it\n";

// A ? in front of a type means "or null". A | means "or".
function greet(?string $name): string
{
    return 'Hello, ' . ($name ?? 'stranger');
}
function describe(int|string $id): string
{
    return 'id ' . $id . ' is ' . get_debug_type($id);
}
echo greet(null), "\n", describe(7), "\n", describe('A7'), "\n";

// A peek at an object: a value with its own functions, called methods.
$day = new DateTimeImmutable('2026-11-27');
echo get_debug_type($day), ': ', $day->format('l, F j, Y'), "\n";
