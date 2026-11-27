<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/i18n.php';

// Pick the language: a choice made on the page, then one remembered in a
// cookie, then the browser's Accept-Language, then English. Never trust input:
// all three come from the visitor, so each must be on our list.
$chosen = $_GET['lang'] ?? null;
if (is_string($chosen) && isset(LANGUAGES[$chosen])) {
    setcookie('lang', $chosen, [
        'expires' => time() + 365 * 24 * 60 * 60,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    $lang = $chosen;
} elseif (is_string($_COOKIE['lang'] ?? null) && isset(LANGUAGES[$_COOKIE['lang']])) {
    $lang = $_COOKIE['lang'];
} else {
    $lang = negotiateLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
}

$messages = require dirname(__DIR__) . "/lang/$lang.php"; // safe: $lang is from our list
$t = fn (string $key, array $vars = []) => translate($messages, $lang, $key, $vars);

// Each product's name in this language, or in English if nobody translated it.
$stmt = connect()->prepare(
    'SELECT p.sku, COALESCE(mine.name, en.name) AS name
       FROM products p
       JOIN product_names en ON en.product_id = p.id AND en.lang = :default
  LEFT JOIN product_names mine ON mine.product_id = p.id AND mine.lang = :lang
   ORDER BY p.id'
);
$stmt->execute(['default' => DEFAULT_LANGUAGE, 'lang' => $lang]);
$products = $stmt->fetchAll();

header('Content-Type: text/html; charset=utf-8');
header("Content-Language: $lang");
// The same URL answers differently by these headers: caches must keep them apart.
header('Vary: Accept-Language, Cookie');
?>
<!doctype html>
<html lang="<?= $lang ?>">
<meta charset="utf-8">
<title><?= htmlspecialchars($t('title')) ?></title>

<h1><?= htmlspecialchars($t('welcome')) ?></h1>
<p><?= htmlspecialchars($t('products', ['count' => count($products)])) ?></p>
<ul>
    <?php foreach ($products as $product): ?>
        <li><?= htmlspecialchars($product['name']) ?></li>
    <?php endforeach; ?>
</ul>

<nav aria-label="<?= htmlspecialchars($t('language')) ?>">
    <?php foreach (LANGUAGES as $code => $name): ?>
        <a href="?lang=<?= $code ?>" hreflang="<?= $code ?>" lang="<?= $code ?>"><?= htmlspecialchars($name) ?></a>
    <?php endforeach; ?>
</nav>
</html>
