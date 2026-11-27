<?php
// The fixed version. Never trust input: every value from the request is
// checked against rules this file owns, and the price never comes from the form.
declare(strict_types=1);

const PRICE_CENTS = 1200;
const MAX_QTY = 10;

$posted = $_SERVER['REQUEST_METHOD'] === 'POST';
$errors = [];
$qty = null;

if ($posted) {
    // 1. Is it there, and is it the type you expect? (It could be missing, or an array.)
    $raw = $_POST['qty'] ?? null;
    if (!is_string($raw)) {
        $errors[] = 'How many mugs?';
    } else {
        // 2. Is it the shape and range you allow? Reject it if not; don't repair it.
        $qty = filter_var($raw, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => MAX_QTY],
        ]);
        if ($qty === false) {
            $errors[] = 'Pick from 1 to ' . MAX_QTY . ' mugs.';
        }
    }
    if ($errors) {
        http_response_code(422);
    }
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Buy mugs</title>

<?php if ($posted && !$errors): ?>
    <p>Order total: $<?= number_format($qty * PRICE_CENTS / 100, 2) ?> for <?= $qty ?> mugs.</p>
<?php endif; ?>
<?php foreach ($errors as $error): ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php endforeach; ?>

<form method="post">
    <p><label>How many mugs, at $<?= number_format(PRICE_CENTS / 100, 2) ?> each?
        <input type="number" name="qty" min="1" max="<?= MAX_QTY ?>" value="1" required></label></p>
    <p><button type="submit">Buy</button></p>
</form>
