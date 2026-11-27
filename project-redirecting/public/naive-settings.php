<?php
// The naive version: it sends you back to whatever "back" says.
// Don't copy this one. Break It attacks it.
require dirname(__DIR__) . '/src/redirect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    setcookie('theme', $_POST['theme'] === 'dark' ? 'dark' : 'light', ['httponly' => true, 'samesite' => 'Lax']);
    redirect($_POST['back'] ?? '/');
}

$back = $_GET['back'] ?? '/';
?>
<!doctype html>
<meta charset="utf-8">
<title>Settings (naive)</title>
<form method="post">
    <input type="hidden" name="back" value="<?= htmlspecialchars($back) ?>">
    <label><input type="radio" name="theme" value="light" checked> Light</label>
    <label><input type="radio" name="theme" value="dark"> Dark</label>
    <button type="submit">Save</button>
</form>
