<?php

declare(strict_types=1);

require __DIR__ . '/after/discount.php';

// The boundary. Never trust input: a discount code comes from whoever typed the URL.
// Unknown codes get a polite answer here; discountedCents() never sees them.
$codes = ['WELCOME10' => 10, 'HALF' => 50];
$code = is_string($_GET['code'] ?? null) ? strtoupper(trim($_GET['code'])) : '';

header('Content-Type: text/plain; charset=utf-8');

if (!array_key_exists($code, $codes)) {
    http_response_code(422);
    echo "That code isn't one of ours.\n";
    exit;
}

echo 'Your price: ', number_format(discountedCents(2000, $codes[$code]) / 100, 2), "\n";
