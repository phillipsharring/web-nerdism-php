<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';

// Never trust input: a product id and a price, both whole numbers.
$id = filter_var($argv[1] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$price = filter_var($argv[2] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
if ($id === false || $price === false) {
    fwrite(STDERR, "Usage: php set-price.php PRODUCT_ID PRICE_CENTS\n");
    exit(1);
}

$statement = $pdo->prepare('
    UPDATE `products`
    SET `price_cents` = ?
    WHERE `id` = ?
');
$statement->execute([$price, $id]);

$cost = sprintf('%d.%02d', intdiv($price, 100), $price % 100);

if ($statement->rowCount() === 1) {
    echo "Product #$id now costs $cost.\n";
    exit;
}

// No row changed. Either there's no such product, or it already had that price.
$exists = $pdo->prepare('
    SELECT COUNT(*)
    FROM `products`
    WHERE `id` = ?
');
$exists->execute([$id]);
if ($exists->fetchColumn() === 0) {
    echo "There's no product #$id.\n";
} else {
    echo "Product #$id already costs $cost. Nothing changed.\n";
}
