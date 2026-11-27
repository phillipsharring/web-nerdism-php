<?php
// BROKEN ON PURPOSE, for the Break It section. Never put this on a server.
// It asks for the card number itself, logs every field "for debugging", and
// keeps the card in the database. Every one of those is a card leak waiting.
require dirname(__DIR__) . '/vendor/autoload.php';
ini_set('log_errors', '1');
ini_set('error_log', dirname(__DIR__) . '/logs/app.log');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('checkout: ' . json_encode($_POST));
    $db = (new Shop\Database())->connect();
    $db->prepare('INSERT INTO naive_orders (email, card_number, card_expiry, card_cvc, total_cents) VALUES (?, ?, ?, ?, ?)')
        ->execute([$_POST['email'], $_POST['card_number'], $_POST['card_expiry'], $_POST['card_cvc'], (int) $_POST['total_cents']]);
    echo "Thanks! We'll charge your card.\n";
    exit;
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Check out (naive)</title>
<form method="post">
    <input type="hidden" name="total_cents" value="2800">
    <p><label>Email <input name="email"></label></p>
    <p><label>Card number <input name="card_number" autocomplete="cc-number"></label></p>
    <p><label>Expiry <input name="card_expiry" placeholder="MM/YY"></label></p>
    <p><label>CVC <input name="card_cvc"></label></p>
    <p><button type="submit">Pay</button></p>
</form>
