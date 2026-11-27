<?php
// Where Stripe sends the visitor afterward. Never trust input: anyone can
// visit this address, with any session id. Ask Stripe what really happened.
declare(strict_types=1);

$id = $_GET['session_id'] ?? '';
if (!is_string($id) || !preg_match('/\Acs_(test|live)_[A-Za-z0-9]+\z/', $id)) {
    http_response_code(404);
    exit('No such order.');
}

$stripe = require __DIR__ . '/stripe.php';
$session = $stripe->checkout->sessions->retrieve($id);

header('Content-Type: text/plain; charset=utf-8');
if ($session->payment_status === 'paid') {
    echo "Paid. Thanks! Your mug is on its way.\n";
} else {
    echo "We haven't received your payment yet.\n";
}
