<?php

declare(strict_types=1);

// php run.php before, or php run.php after.
$version = $argv[1] ?? '';
if (!in_array($version, ['before', 'after'], true)) {
    fwrite(STDERR, "Usage: php run.php before|after\n");
    exit(1);
}

require __DIR__ . "/$version/checkout.php";

$verified = ['email' => 'alice@example.com', 'verified' => true];
$unverified = ['email' => 'bob@example.com', 'verified' => false];

echo checkoutMessage(null, ['mug']), "\n";
echo checkoutMessage($unverified, ['mug']), "\n";
echo checkoutMessage($verified, []), "\n";
echo checkoutMessage($verified, array_fill(0, 25, 'mug')), "\n";
echo checkoutMessage($verified, ['mug', 'shirt', 'hat']), "\n";
