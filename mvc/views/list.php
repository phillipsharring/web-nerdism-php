<?php

declare(strict_types=1);

// The view: HTML only. Everything it prints is escaped.
$e = fn(string $text) => htmlspecialchars($text);
?>
<!doctype html>
<meta charset="utf-8">
<title>Menu</title>
<h1>Menu<?= $view['category'] ? ': ' . $e($view['category']) : '' ?></h1>
<p>
    <a href="/">Everything</a>
<?php foreach ($view['categories'] as $category): ?>
    <a href="/?category=<?= urlencode($category) ?>"><?= $e($category) ?></a>
<?php endforeach; ?>
</p>
<ul>
<?php foreach ($view['items'] as $item): ?>
    <li><a href="/item.php?id=<?= $item->id ?>"><?= $e($item->name) ?></a> <?= number_format($item->priceCents / 100, 2) ?></li>
<?php endforeach; ?>
</ul>
