<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/helpers.php';

use Shop\Database;
use Shop\Orders;
use Shop\Pricing;

startSession();
header('Cache-Control: private, no-store');

// Only the browser that placed the order can see it. Knowing the id isn't
// enough, and an order that isn't yours looks the same as one that doesn't exist.
$id = is_string($_GET['id'] ?? null) ? $_GET['id'] : '';
$db = (new Database())->connect();
$order = in_array($id, $_SESSION['orders'] ?? [], true) ? (new Orders($db, new Pricing($db)))->find($id) : null;
if ($order === null) {
    http_response_code(404);
    exit("<!doctype html>\n<meta charset=\"utf-8\">\n<title>Not found</title>\n<p>There's no order here.</p>\n");
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Your order</title>
<h1>Thanks, <?= e($order['name']) ?></h1>
<ul>
    <?php foreach ($order['items'] as $item): ?>
        <li><?= $item['quantity'] ?> &times; <?= e($item['name']) ?>, <?= money($item['line_cents']) ?></li>
    <?php endforeach; ?>
</ul>
<p><strong>Total: <?= money($order['total_cents']) ?></strong></p>
<p>Status: <?= e(str_replace('_', ' ', $order['status'])) ?></p>
<?php if ($order['status'] === 'awaiting_payment' && isset($_GET['paid'])): ?>
    <?php // Back from Stripe. That's not proof of payment: the webhook is. ?>
    <p>Thanks! We're confirming your payment with Stripe. Reload in a moment.</p>
<?php elseif ($order['status'] === 'awaiting_payment'): ?>
    <form method="post" action="/pay.php">
        <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
        <input type="hidden" name="order" value="<?= e($order['public_id']) ?>">
        <p><button type="submit">Pay <?= money($order['total_cents']) ?> with Stripe</button></p>
    </form>
<?php else: ?>
    <p>Paid. Thank you!</p>
<?php endif; ?>
