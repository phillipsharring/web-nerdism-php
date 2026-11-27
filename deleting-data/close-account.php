<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';

// Never trust input.
$id = filter_var($argv[1] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    fwrite(STDERR, "Usage: php close-account.php USER_ID\n");
    exit(1);
}

// A soft delete: mark the row, and leave it, and everything pointing at it, in place.
$statement = $pdo->prepare('
    UPDATE `users`
    SET `deleted_at` = NOW()
    WHERE `id` = ?
    AND `deleted_at` IS NULL
');
$statement->execute([$id]);

echo $statement->rowCount() === 1 ? "Closed account #$id.\n" : "No open account #$id.\n";

// From now on, every query for users has to leave out the closed ones.
$users = $pdo->query('
    SELECT `name`
    FROM `users`
    WHERE `deleted_at` IS NULL
    ORDER BY `name`
')->fetchAll(PDO::FETCH_COLUMN);

echo 'Open accounts: ', $users ? implode(', ', $users) : '(none)', "\n";
