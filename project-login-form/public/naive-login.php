<?php
// The naive sign-in: guess as often as you like, and it keeps whatever
// session id the browser brought with it. Don't copy this one.
require dirname(__DIR__) . '/src/db.php';

session_start(); // PHP's defaults: no strict mode

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare('SELECT password_hash FROM users WHERE email = ?');
    $stmt->execute([$_POST['email']]);
    $hash = $stmt->fetchColumn();

    if ($hash && password_verify($_POST['password'], $hash)) {
        $_SESSION['user'] = $_POST['email'];
        header('Location: /account.php', true, 303);
        exit;
    }
    echo '<p>Wrong email or password.</p>';
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Sign in (naive)</title>
<form method="post">
    <p><label>Email <input type="email" name="email" required></label></p>
    <p><label>Password <input type="password" name="password" required></label></p>
    <p><button type="submit">Sign in</button></p>
</form>
