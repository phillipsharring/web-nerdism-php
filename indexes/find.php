<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';

// Never trust input: it has to look like an email address before it's a query.
$email = filter_var($argv[1] ?? '', FILTER_VALIDATE_EMAIL);
if ($email === false) {
    fwrite(STDERR, "Usage: php find.php EMAIL\n");
    exit(1);
}

$statement = $pdo->prepare('
    SELECT `id`, `name`
    FROM `customers`
    WHERE `email` = ?
');

$start = hrtime(true);
$statement->execute([$email]);
$customer = $statement->fetch();
$ms = (hrtime(true) - $start) / 1_000_000;

if ($customer === false) {
    echo 'No one has that address. ';
} else {
    echo "Found {$customer['name']} (#{$customer['id']}). ";
}
printf("The query took %.1f ms.\n", $ms);
