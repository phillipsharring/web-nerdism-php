<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';

// Never trust input: two ids, both whole numbers.
$customerId = filter_var($argv[1] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$productId = filter_var($argv[2] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($customerId === false || $productId === false) {
    fwrite(STDERR, "Usage: php place-order.php CUSTOMER_ID PRODUCT_ID\n");
    exit(1);
}

$product = $pdo->prepare('
    SELECT `price_cents`
    FROM `products`
    WHERE `id` = ?
');
$product->execute([$productId]);
$price = $product->fetchColumn();
if ($price === false) {
    fwrite(STDERR, "There's no product #$productId.\n");
    exit(1);
}

// The order and the copied count change together, or not at all.
// Transactions, later in this section, explains how.
$pdo->beginTransaction();

$insert = $pdo->prepare('
    INSERT INTO `orders` (`customer_id`, `product_id`, `price_cents`)
    VALUES (?, ?, ?)
');
$insert->execute([$customerId, $productId, $price]);
$orderId = $pdo->lastInsertId();

$count = $pdo->prepare('
    UPDATE `customers`
    SET `order_count` = `order_count` + 1
    WHERE `id` = ?
');
$count->execute([$customerId]);

$pdo->commit();

echo "Order #$orderId placed, at ", sprintf('%d.%02d', intdiv($price, 100), $price % 100), "\n";
