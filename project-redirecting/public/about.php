<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/redirect.php';
?>
<!doctype html>
<meta charset="utf-8">
<title>About</title>
<body class="<?= theme() ?>">
<h1>About</h1>
<p>This page used to be at /about-us.php. Old links still work.</p>
<p><a href="/">Home</a> &middot; <a href="/settings.php?back=/about.php">Settings</a></p>
