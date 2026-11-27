<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/helpers.php';

use Shop\Cart;
use Shop\Database;
use Shop\Pricing;

startSession();
$cart = new Cart($_SESSION['cart'] ?? []);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();

    // Never trust input. The form sends an action, a product id and a
    // quantity, and that's all we read. If it sends a price, nobody looks.
    $action = $_POST['action'] ?? '';
    $id = filter_var($_POST['product'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $quantity = filter_var($_POST['quantity'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['default' => 1]]);

    if ($id !== false && $action === 'add' && $quantity >= 1) {
        $cart->add($id, $quantity);
    } elseif ($id !== false && $action === 'set') {
        $cart->set($id, $quantity);
    } elseif ($id !== false && $action === 'remove') {
        $cart->remove($id);
    }
    $_SESSION['cart'] = $cart->items();

    header('Location: /cart.php', true, 303);
    exit;
}

$priced = (new Pricing((new Database())->connect()))->price($cart);
header('Cache-Control: private, no-store');
?>
<!doctype html>
<meta charset="utf-8">
<title>Your cart</title>
<h1>Your cart</h1>
<p><a href="/">Keep shopping</a></p>
<?php if ($priced['lines'] === []): ?>
    <p>Your cart is empty.</p>
<?php else: ?>
    <table>
        <tr><th>Item</th><th>Price</th><th>Quantity</th><th>Total</th><th></th></tr>
        <?php foreach ($priced['lines'] as $line): ?>
            <tr>
                <td><?= e($line['name']) ?><?= $line['limited'] ? ' (only ' . $line['quantity'] . ' left)' : '' ?></td>
                <td><?= money($line['unit_cents']) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
                        <input type="hidden" name="action" value="set">
                        <input type="hidden" name="product" value="<?= $line['id'] ?>">
                        <input type="number" name="quantity" value="<?= $line['quantity'] ?>" min="0" max="<?= Cart::MAX_QUANTITY ?>" aria-label="Quantity">
                        <button type="submit">Update</button>
                    </form>
                </td>
                <td><?= money($line['line_cents']) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="product" value="<?= $line['id'] ?>">
                        <button type="submit">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr><th colspan="3">Total</th><th><?= money($priced['total_cents']) ?></th><th></th></tr>
    </table>
    <p><a href="/checkout.php">Check out</a></p>
<?php endif; ?>
