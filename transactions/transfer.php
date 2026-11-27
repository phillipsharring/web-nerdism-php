<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';
[$from, $to, $cents] = require __DIR__ . '/args.php';

$pdo->beginTransaction();

try {
    // Take the money, but only if it's there.
    $debit = $pdo->prepare('
        UPDATE `accounts`
        SET `balance_cents` = `balance_cents` - ?
        WHERE `id` = ?
        AND `balance_cents` >= ?
    ');
    $debit->execute([$cents, $from, $cents]);
    if ($debit->rowCount() !== 1) {
        throw new RuntimeException("Account #$from doesn't exist, or doesn't have $cents cents.");
    }

    $credit = $pdo->prepare('
        UPDATE `accounts`
        SET `balance_cents` = `balance_cents` + ?
        WHERE `id` = ?
    ');
    $credit->execute([$cents, $to]);
    if ($credit->rowCount() !== 1) {
        throw new RuntimeException("Account #$to doesn't exist.");
    }

    $pdo->commit();
    echo "Transferred $cents cents from #$from to #$to.\n";
} catch (Throwable $e) {
    // Anything went wrong: undo everything since beginTransaction().
    $pdo->rollBack();
    echo 'Nothing transferred. ', $e->getMessage(), "\n";
    exit(1);
}
