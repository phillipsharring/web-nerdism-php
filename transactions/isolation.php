<?php
declare(strict_types=1);

// Two connections, like two visitors at once.
$a = require __DIR__ . '/db.php';
$b = require __DIR__ . '/db.php';

$balance = '
    SELECT `balance_cents`
    FROM `accounts`
    WHERE `id` = 1
';

$a->beginTransaction();
$a->exec('
    UPDATE `accounts`
    SET `balance_cents` = `balance_cents` + 1
    WHERE `id` = 1
');

echo 'A, inside its transaction, sees: ', $a->query($balance)->fetchColumn(), "\n";
echo 'B, outside it, sees:             ', $b->query($balance)->fetchColumn(), "\n";

$a->commit();

echo 'B, after A commits, sees:        ', $b->query($balance)->fetchColumn(), "\n";
