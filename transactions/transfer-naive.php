<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';
[$from, $to, $cents] = require __DIR__ . '/args.php';

// Two separate statements. Each one is saved the moment it runs.
$pdo->prepare('
    UPDATE `accounts`
    SET `balance_cents` = `balance_cents` - ?
    WHERE `id` = ?
')->execute([$cents, $from]);

$pdo->prepare('
    UPDATE `accounts`
    SET `balance_cents` = `balance_cents` + ?
    WHERE `id` = ?
')->execute([$cents, $to]);

echo "Transferred $cents cents from #$from to #$to.\n";
