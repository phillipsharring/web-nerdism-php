<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/helpers.php';

use Shop\Cart;
use Shop\CheckoutProblem;
use Shop\Database;
use Shop\Orders;
use Shop\Pricing;

startSession();
header('Cache-Control: private, no-store');
$cart = new Cart($_SESSION['cart'] ?? []);
$db = (new Database())->connect();
$pricing = new Pricing($db);
$errors = [];
$email = $name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();

    // Never trust input. Two fields, checked, and nothing else is read.
    // There are no card fields, here or anywhere: the card number goes to
    // the payment provider's page, and never passes through this server.
    $email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
    $name = is_string($_POST['name'] ?? null) ? trim($_POST['name']) : '';
    $token = is_string($_POST['checkout'] ?? null) ? $_POST['checkout'] : '';

    // Pressed twice, or the back button and again: this form already made an order.
    if (isset($_SESSION['placed'][$token])) {
        header('Location: /order.php?id=' . $_SESSION['placed'][$token], true, 303);
        exit;
    }

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($email) > 254) {
        $errors[] = 'An email address, please, for the receipt.';
    }
    if ($name === '' || mb_strlen($name) > 100) {
        $errors[] = 'Your name, please.';
    }
    if (!hash_equals($_SESSION['checkout'] ?? '', $token)) {
        $errors[] = 'That form expired. Check your cart and try again.';
    }

    if (!$errors) {
        try {
            $publicId = (new Orders($db, $pricing))->place($cart, $email, $name, $token);
            $_SESSION['orders'][] = $publicId;   // this browser may see this order
            $_SESSION['placed'][$token] = $publicId;
            $_SESSION['cart'] = [];
            unset($_SESSION['checkout']);
            header('Location: /order.php?id=' . $publicId, true, 303);
            exit;
        } catch (CheckoutProblem $e) {
            $errors[] = $e->getMessage();
        }
    }
}

$priced = $pricing->price($cart);
// A fresh token for each checkout form: the order is placed once per token.
$_SESSION['checkout'] ??= bin2hex(random_bytes(32));
?>
<!doctype html>
<meta charset="utf-8">
<title>Check out</title>
<h1>Check out</h1>
<?php if ($priced['lines'] === []): ?>
    <p>Your cart is empty. <a href="/">Go shopping</a>.</p>
<?php else: ?>
    <ul>
        <?php foreach ($priced['lines'] as $line): ?>
            <li><?= $line['quantity'] ?> &times; <?= e($line['name']) ?>, <?= money($line['line_cents']) ?></li>
        <?php endforeach; ?>
    </ul>
    <p><strong>Total: <?= money($priced['total_cents']) ?></strong></p>
    <?php foreach ($errors as $error): ?>
        <p><?= e($error) ?></p>
    <?php endforeach; ?>
    <form method="post">
        <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
        <input type="hidden" name="checkout" value="<?= $_SESSION['checkout'] ?>">
        <p><label>Email <input type="email" name="email" value="<?= e($email) ?>" required autocomplete="email"></label></p>
        <p><label>Name <input name="name" value="<?= e($name) ?>" required maxlength="100" autocomplete="name"></label></p>
        <p>You'll pay on the next page, on our payment provider's site.</p>
        <p><button type="submit">Place order</button></p>
    </form>
<?php endif; ?>
