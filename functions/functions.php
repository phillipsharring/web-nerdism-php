<?php
declare(strict_types=1);

// Takes an int, gives back a string. Nothing else goes in or comes out.
function format_cents(int $cents): string
{
    return '$' . number_format($cents / 100, 2);
}

// A parameter with a default can be left off.
function greet(string $name, string $greeting = 'Hello'): string
{
    return $greeting . ', ' . $name . '!';
}

// Checks input and returns a whole number from 1 to 10, or null if it isn't one.
// Never trust input: it might not even be a string.
function quantity_from(mixed $input): ?int
{
    if (!is_string($input)) {
        return null;
    }
    $number = filter_var($input, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 10]]);
    return $number === false ? null : $number;
}

// ...$prices collects any number of arguments into an array.
function total(int ...$prices): int
{
    return array_sum($prices);
}

// void: does something, returns nothing.
function log_line(string $message): void
{
    echo '[log] ', $message, "\n";
}

echo format_cents(1999), "\n";
echo greet('Alice'), "\n";
echo greet('Bob', 'Welcome back'), "\n";
echo greet(greeting: 'Hi', name: 'Carol'), "\n"; // named arguments, in any order

var_dump(quantity_from('3'), quantity_from('30'), quantity_from('three'), quantity_from(['3']));

echo format_cents(total(2000, 1200, 300)), "\n";
log_line('Order total worked out');

// A function can be passed around like a value.
$lengths = array_map(strlen(...), ['a', 'bb', 'ccc']);
echo implode(', ', $lengths), "\n";

echo format_cents('1999'), "\n"; // strict_types: a string isn't an int
