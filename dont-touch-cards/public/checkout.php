<?php
// Starts a payment, and sends the visitor to Stripe's own page to make it.
// No card field exists anywhere on this site, so no card number can reach it.
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Use the button on the shop page.');
}

$stripe = require __DIR__ . '/stripe.php';
$shop = getenv('SHOP_URL');

// The price and the product come from your code, never from the request.
$session = $stripe->checkout->sessions->create([
    'mode' => 'payment',
    'line_items' => [[
        'quantity' => 1,
        'price_data' => [
            'currency' => 'usd',
            'unit_amount' => 1200, // cents: $12.00
            'product_data' => ['name' => 'One mug'],
        ],
    ]],
    'success_url' => $shop . '/thanks.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => $shop . '/',
]);

header('Location: ' . $session->url, true, 303);
