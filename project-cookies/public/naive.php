<?php
// The naive version: a cookie with no flags, printed just as it came back.
// Don't copy this one. Break It attacks it.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    setcookie('name', $_POST['name'] ?? '');
    header('Location: /naive.php', true, 303);
    exit;
}

$name = $_COOKIE['name'] ?? '';
?>
<!doctype html>
<meta charset="utf-8">
<title>Remember me (naive)</title>
<?php if ($name !== ''): ?>
    <p>Welcome back, <?= $name ?>.</p>
<?php endif; ?>
<form method="post">
    <label>Your name <input name="name"></label>
    <button type="submit">Remember me</button>
</form>
