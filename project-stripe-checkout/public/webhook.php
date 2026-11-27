<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Shop\Database;
use Shop\Orders;
use Shop\Pricing;
use Shop\StripeSignature;

// Stripe calls this, server to server, when something happens to a payment.
// This, not the visitor coming back to the success page, is what marks an
// order paid. Anyone can visit a success page. Only Stripe can sign this.

header('Content-Type: application/json');
$payload = (string) file_get_contents('php://input');   // the raw body: the signature covers these exact bytes

try {
    $event = (new StripeSignature((string) getenv('STRIPE_WEBHOOK_SECRET')))
        ->verify($payload, $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '');
} catch (Throwable $e) {
    http_response_code(400);
    exit(json_encode(['error' => 'Signature check failed.']) . "\n");
}

$type = $event['type'] ?? '';
$session = $event['data']['object'] ?? [];

// A card payment is paid when the session completes. Some other payment
// methods finish later, with their own event.
$paid = ($type === 'checkout.session.completed' && ($session['payment_status'] ?? '') === 'paid')
    || $type === 'checkout.session.async_payment_succeeded';

if ($paid && is_string($session['client_reference_id'] ?? null)) {
    $db = (new Database())->connect();
    $changed = (new Orders($db, new Pricing($db)))->markPaid(
        $session['client_reference_id'],
        (int) ($session['amount_total'] ?? -1),
        (string) ($session['currency'] ?? ''),
    );
    error_log(sprintf('webhook %s for order %s: %s', $type, $session['client_reference_id'], $changed ? 'marked paid' : 'no change'));
}

// Always a quick 200 for a signed event, handled or not, or Stripe keeps retrying it.
echo json_encode(['received' => true]), "\n";
