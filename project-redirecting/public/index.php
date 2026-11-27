<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/redirect.php';
?>
<!doctype html>
<meta charset="utf-8">
<title>Home</title>
<body class="<?= theme() ?>">
<h1>Home</h1>
<p><a href="/about.php">About</a> &middot; <a href="/settings.php?back=/">Settings</a></p>
