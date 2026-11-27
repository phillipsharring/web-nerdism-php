<?php
// The naive version: it believes whatever the form sends. Don't copy this one.
declare(strict_types=1);

$posted = $_SERVER['REQUEST_METHOD'] === 'POST';
?>
<!doctype html>
<meta charset="utf-8">
<title>Buy mugs (naive)</title>

<?php if ($posted): ?>
    <p>Order total: $<?= number_format($_POST['qty'] * $_POST['price'] / 100, 2) ?> for <?= $_POST['qty'] ?> mugs.</p>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="price" value="1200">
    <p><label>How many mugs, at $12.00 each?
        <input type="number" name="qty" min="1" max="10" value="1" required></label></p>
    <p><button type="submit">Buy</button></p>
</form>
