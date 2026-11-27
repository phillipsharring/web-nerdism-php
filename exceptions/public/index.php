<?php
declare(strict_types=1);

require dirname(__DIR__) . '/handler.php';

// Our own kind of exception, for "the request was wrong," so we can catch just that.
class BadRequest extends RuntimeException
{
}

// Never trust input: anything in the query string is whatever someone typed.
function wholeNumber(string $name, int $min, int $max): int
{
    $value = filter_var($_GET[$name] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => $min, 'max_range' => $max],
    ]);
    if ($value === false) {
        throw new BadRequest("{$name} must be a whole number from {$min} to {$max}.");
    }
    return $value;
}

// Splits a bill in cents, so the shares always add up to the total.
function splitBill(int $cents, int $people): array
{
    if ($people < 1) {
        throw new InvalidArgumentException('Nobody to split it between.');
    }
    $shares = array_fill(0, $people, intdiv($cents, $people));
    for ($i = 0; $i < $cents % $people; $i++) {
        $shares[$i]++;
    }
    return $shares;
}

header('Content-Type: text/plain; charset=utf-8');

try {
    $shares = splitBill(wholeNumber('cents', 1, 1_000_000), wholeNumber('people', 1, 50));
} catch (BadRequest $e) {
    // We know what went wrong, and so should they.
    http_response_code(400);
    echo $e->getMessage(), "\n";
    exit;
}

echo 'Shares in cents: ', implode(', ', $shares), "\n";
