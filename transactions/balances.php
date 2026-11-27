<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';

$accounts = $pdo->query('
    SELECT `id`, `name`, `balance_cents`
    FROM `accounts`
    ORDER BY `id`
');

$total = 0;
foreach ($accounts as $account) {
    echo "#{$account['id']} {$account['name']}: {$account['balance_cents']}\n";
    $total += $account['balance_cents'];
}
echo "Total: $total\n";
