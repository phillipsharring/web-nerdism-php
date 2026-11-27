<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';

// Never trust input: a real-looking email address, and a name of a sane length.
$email = filter_var($argv[1] ?? '', FILTER_VALIDATE_EMAIL);
$name = trim($argv[2] ?? '');
if ($email === false || $name === '' || mb_strlen($name) > 100) {
    fwrite(STDERR, "Usage: php subscribe.php EMAIL NAME\n");
    exit(1);
}

// Add the subscriber, or, if the email is already here, update the name.
$statement = $pdo->prepare('
    INSERT INTO `subscribers` (`email`, `name`)
    VALUES (?, ?) AS `new`
    ON DUPLICATE KEY UPDATE `name` = `new`.`name`
');
$statement->execute([$email, $name]);

// For an upsert, MySQL reports 1 for a new row, 2 for an update, 0 for no change.
$changed = $statement->rowCount();
if ($changed === 1) {
    echo 'Added subscriber #', $pdo->lastInsertId(), "\n";
} elseif ($changed === 2) {
    echo "Updated $email\n";
} else {
    echo "$email was already subscribed, as $name\n";
}
