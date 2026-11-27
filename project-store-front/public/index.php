<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/money.php';
require dirname(__DIR__) . '/src/catalog.php';

$db = connect();
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$e = fn (mixed $value): string => htmlspecialchars((string) $value);

// One product: /products/black-mug
if (preg_match('#^/products/([a-z0-9-]{1,100})$#', $path, $m)) {
    $product = product($db, $m[1]);
    if ($product === null) {
        http_response_code(404);
        exit("<!doctype html>\n<meta charset=\"utf-8\">\n<title>Not found</title>\n<p>We don't have that.</p>\n");
    }
    ?>
<!doctype html>
<meta charset="utf-8">
<title><?= $e($product['name']) ?></title>
<p><a href="/">Shop</a> &rsaquo; <a href="/?category=<?= urlencode($product['category']) ?>"><?= $e($product['category_name']) ?></a></p>
<h1><?= $e($product['name']) ?></h1>
<p><?= $e($product['description']) ?></p>
<p><strong><?= money($product['price_cents']) ?></strong></p>
<p><?= $product['stock'] > 0 ? 'In stock' : 'Sold out' ?></p>
<p>SKU <?= $e($product['sku']) ?></p>
    <?php
    exit;
}
if ($path !== '/') {
    http_response_code(404);
    exit("<!doctype html>\n<meta charset=\"utf-8\">\n<title>Not found</title>\n<p>We don't have that.</p>\n");
}

// The list: /?category=mugs&sort=price-low. Never trust input: the category
// must be one of ours, and the sort one of the keys in SORTS.
$categories = categories($db);
$category = $_GET['category'] ?? null;
if (!is_string($category) || !in_array($category, array_column($categories, 'slug'), true)) {
    $category = null;
}
$sort = $_GET['sort'] ?? 'name';
if (!is_string($sort) || !isset(SORTS[$sort])) {
    $sort = 'name';
}
$products = products($db, $category, $sort);

/** A link to this list with some settings changed. */
$link = fn (array $change): string => '/?' . http_build_query(array_merge(['category' => $category, 'sort' => $sort], $change));
?>
<!doctype html>
<meta charset="utf-8">
<title>Shop</title>
<h1>Shop</h1>
<nav aria-label="Categories">
    <a href="<?= $e($link(['category' => null])) ?>"<?= $category === null ? ' aria-current="page"' : '' ?>>Everything</a>
    <?php foreach ($categories as $c): ?>
        <a href="<?= $e($link(['category' => $c['slug']])) ?>"<?= $category === $c['slug'] ? ' aria-current="page"' : '' ?>><?= $e($c['name']) ?></a>
    <?php endforeach; ?>
</nav>
<p>Sort:
    <a href="<?= $e($link(['sort' => 'name'])) ?>">name</a>,
    <a href="<?= $e($link(['sort' => 'price-low'])) ?>">price, low first</a>,
    <a href="<?= $e($link(['sort' => 'price-high'])) ?>">price, high first</a>
</p>
<ul>
    <?php foreach ($products as $p): ?>
        <li>
            <a href="/products/<?= urlencode($p['slug']) ?>"><?= $e($p['name']) ?></a>,
            <?= money($p['price_cents']) ?><?= $p['stock'] > 0 ? '' : ' (sold out)' ?>
        </li>
    <?php endforeach; ?>
</ul>
