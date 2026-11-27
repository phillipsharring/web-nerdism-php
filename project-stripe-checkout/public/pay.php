<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/helpers.php';

use Shop\Database;
use Shop\Orders;
use Shop\Payments;
use Shop\Pricing;
use Stripe\StripeClient;

startSession();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

$key = stripeTestKey();
if ($key === null) {
    http_response_code(503);
    exit("Payments aren't set up. Put a Stripe test key (sk_test_...) in .env; this example never uses a live one.\n");
}
checkCsrf();

$db = (new Database())->connect();
$orders = new Orders($db, new Pricing($db));
$id = is_string($_POST['order'] ?? null) ? $_POST['order'] : '';
$order = in_array($id, $_SESSION['orders'] ?? [], true) ? $orders->find($id) : null;
if ($order === null || $order['status'] !== 'awaiting_payment') {
    http_response_code(404);
    exit("There's no order here to pay for.\n");
}

[$sessionId, $url] = (new Payments(new StripeClient($key), rtrim(getenv('SITE_URL'), '/')))->startCheckout($order);
$orders->attachSession($order['public_id'], $sessionId);

// Off to Stripe's page. The card is typed there, and never comes here.
header('Location: ' . $url, true, 303);
