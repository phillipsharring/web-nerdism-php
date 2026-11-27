<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/redirect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = ($_POST['theme'] ?? '') === 'dark' ? 'dark' : 'light';
    setcookie('theme', $theme, [
        'expires' => time() + 60 * 60 * 24 * 365,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    redirect(local_path($_POST['back'] ?? null));
}

$back = local_path($_GET['back'] ?? null);
?>
<!doctype html>
<meta charset="utf-8">
<title>Settings</title>
<body class="<?= theme() ?>">
<form method="post">
    <input type="hidden" name="back" value="<?= htmlspecialchars($back) ?>">
    <label><input type="radio" name="theme" value="light"<?= theme() === 'light' ? ' checked' : '' ?>> Light</label>
    <label><input type="radio" name="theme" value="dark"<?= theme() === 'dark' ? ' checked' : '' ?>> Dark</label>
    <button type="submit">Save</button>
</form>
