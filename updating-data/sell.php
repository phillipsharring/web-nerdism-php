<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';

// Never trust input.
$id = filter_var($argv[1] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    fwrite(STDERR, "Usage: php sell.php PRODUCT_ID\n");
    exit(1);
}

// Check and change in one statement: take one, only if there's one to take.
$statement = $pdo->prepare('
    UPDATE `products`
    SET `stock` = `stock` - 1
    WHERE `id` = ?
    AND `stock` > 0
');
$statement->execute([$id]);

if ($statement->rowCount() === 0) {
    echo "Sorry, product #$id is sold out.\n";
    exit(1);
}

$left = $pdo->prepare('
    SELECT `stock`
    FROM `products`
    WHERE `id` = ?
');
$left->execute([$id]);
echo 'Sold one. ', $left->fetchColumn(), " left.\n";
