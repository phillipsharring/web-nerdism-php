<?php
declare(strict_types=1);
?>
<!doctype html>
<meta charset="utf-8">
<title>Mug shop</title>

<p>One mug, $12.00.</p>

<!-- No card fields. The button asks your server to start a Stripe Checkout,
     and the visitor types their card on Stripe's page, not yours. -->
<form method="post" action="/checkout.php">
    <p><button type="submit">Buy it</button></p>
</form>
