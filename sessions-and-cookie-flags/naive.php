<?php
// The naive version: PHP's default session settings, and no new session id
// at login. Don't copy this one.
declare(strict_types=1);

require __DIR__ . '/login.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($email = checkLogin()) !== null) {
    $_SESSION['email'] = $email;
}
$who = $_SESSION['email'] ?? null;
?>
<!doctype html>
<meta charset="utf-8">
<title>Members (naive)</title>

<p><?= $who !== null ? 'Logged in as ' . e($who) . '.' : 'Not logged in.' ?></p>

<form method="post">
    <p><label>Email <input type="email" name="email" required></label></p>
    <p><label>Password <input type="password" name="password" required></label></p>
    <p><button type="submit">Log in</button></p>
</form>
