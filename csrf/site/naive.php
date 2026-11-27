<?php
// The naive version: any POST that carries the session cookie is believed.
// Don't copy this one.
declare(strict_types=1);

require __DIR__ . '/account.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($email = newEmail()) !== null) {
    $_SESSION['email'] = $email;
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Your account (naive)</title>

<p>Your email address: <?= e($_SESSION['email']) ?></p>

<form method="post">
    <p><label>New email <input type="email" name="email" required></label></p>
    <p><button type="submit">Change it</button></p>
</form>
