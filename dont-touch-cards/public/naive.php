<?php
// The naive version: a card form on your own page, posted to your own server.
// Don't copy this one. It never charges anything; it only shows where the
// numbers end up.
declare(strict_types=1);

$posted = $_SERVER['REQUEST_METHOD'] === 'POST';
if ($posted) {
    // The debugging line every app has somewhere. It just wrote a card number
    // to your log.
    error_log('New order: ' . json_encode($_POST));
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Mug shop (naive)</title>

<?php if ($posted): ?>
    <p>Thanks! (Nothing was charged. But look at your log.)</p>
<?php endif; ?>

<form method="post">
    <p>One mug, $12.00.</p>
    <p><label>Card number <input name="card_number" inputmode="numeric" autocomplete="cc-number" required></label></p>
    <p><label>Expiry <input name="card_expiry" placeholder="MM/YY" autocomplete="cc-exp" required></label></p>
    <p><label>CVC <input name="card_cvc" inputmode="numeric" autocomplete="cc-csc" required></label></p>
    <p><button type="submit">Pay</button></p>
</form>
