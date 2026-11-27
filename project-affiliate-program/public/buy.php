<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/Affiliates.php';

// A stand-in for the checkout from the shop projects: one product, one price.
const PRICE_CENTS = 4999;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

$email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    http_response_code(422);
    exit("An email address, please.\n");
}

$affiliates = new Affiliates(connect());
// The cookie is input: whatever it says is looked up again, and a code that
// isn't a real, active affiliate counts for nothing.
$affiliate = $affiliates->find($_COOKIE[Affiliates::COOKIE] ?? null);
$affiliates->recordOrder($email, PRICE_CENTS, $affiliate);

header('Content-Type: text/plain; charset=utf-8');
echo "Thanks for your order!\n";
