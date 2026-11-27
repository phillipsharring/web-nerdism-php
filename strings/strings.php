<?php
declare(strict_types=1);

$name = 'Alice';
$count = 3;

// Single quotes: exactly what you typed. Double quotes: variables and \n are filled in.
echo 'Hello, $name\n', "\n";
echo "Hello, $name\n";
echo "You have {$count} items\n"; // braces mark where the variable's name ends

// A heredoc: double-quote rules, over several lines.
// The closing marker's indent is taken off every line.
$order = <<<TEXT
    Order for $name
    Items: $count
    TEXT;
echo $order, "\n";

// The everyday functions, on something a person typed.
$typed = '  Alice@Example.COM  ';
$email = strtolower(trim($typed));
echo "[$email]\n";
echo 'Length: ', strlen($email), "\n";
var_dump(str_contains($email, '@'), str_starts_with($email, 'alice'), str_ends_with($email, '.com'));
echo str_replace('example.com', 'example.org', $email), "\n";
echo substr($email, 0, 5), "\n"; // 5 from position 0
echo ucfirst('alice'), ' ', strtoupper('quiet'), "\n";

// Split a string into an array, and join an array into a string.
print_r(explode('@', $email));
echo implode(', ', ['red', 'green', 'blue']), "\n";

// Patterns and padding.
echo sprintf('[%-8s] [%6.2f] [%03d]', 'Shirt', 19.5, 7), "\n";
echo str_pad('42', 6, '.', STR_PAD_LEFT), "\n";
echo number_format(1234567.891, 2), "\n";
