<?php

declare(strict_types=1);

$e = fn(string $text) => htmlspecialchars($text);
?>
<!doctype html>
<meta charset="utf-8">
<title><?= $e($view['item']->name) ?></title>
<h1><?= $e($view['item']->name) ?></h1>
<p><?= $e($view['item']->category) ?>, <?= number_format($view['item']->priceCents / 100, 2) ?></p>
<p><a href="/">Back to the menu</a></p>
