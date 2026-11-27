<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/helpers.php';

use Shop\Cart;
use Shop\Database;
use Shop\Pricing;

startSession();
$cart = new Cart($_SESSION['cart'] ?? []);
$db = (new Database())->connect();
// Count what's really buyable, not every id in the session.
$count = array_sum(array_column((new Pricing($db))->price($cart)['lines'], 'quantity'));
$products = $db
    ->query('SELECT id, name, price_cents, stock FROM products WHERE active = 1 ORDER BY name')->fetchAll();
?>
<!doctype html>
<meta charset="utf-8">
<title>Shop</title>
<h1>Shop</h1>
<p><a href="/cart.php">Cart (<?= $count ?>)</a></p>
<ul>
    <?php foreach ($products as $p): ?>
        <li>
            <?= e($p['name']) ?>, <?= money($p['price_cents']) ?>
            <?php if ($p['stock'] > 0): ?>
                <form method="post" action="/cart.php">
                    <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product" value="<?= $p['id'] ?>">
                    <label>Quantity <input type="number" name="quantity" value="1" min="1" max="<?= Cart::MAX_QUANTITY ?>"></label>
                    <button type="submit">Add to cart</button>
                </form>
            <?php else: ?>
                (sold out)
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
