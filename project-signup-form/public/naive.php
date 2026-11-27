<?php
// The naive signup: it "hashes" the password with md5().
// Don't copy this one. Break It attacks it.
require dirname(__DIR__) . '/src/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
    $stmt->execute([$_POST['email'], md5($_POST['password'])]);
    echo '<p>Account created.</p>';
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Sign up (naive)</title>
<form method="post">
    <p><label>Email <input type="email" name="email" required></label></p>
    <p><label>Password <input type="password" name="password" required></label></p>
    <p><button type="submit">Sign up</button></p>
</form>
