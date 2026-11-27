<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';

// The database's own record of which migrations it has had.
$pdo->exec('
    CREATE TABLE IF NOT EXISTS `migrations` (
        `name` VARCHAR(200) NOT NULL,
        `applied_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`name`)
    )
');

$applied = $pdo->query('
    SELECT `name`
    FROM `migrations`
')->fetchAll(PDO::FETCH_COLUMN);

// Every migration file, oldest first. The date at the front of each name sets the order.
$files = glob(__DIR__ . '/migrations/*.sql');
sort($files);

$record = $pdo->prepare('
    INSERT INTO `migrations` (`name`)
    VALUES (?)
');

$count = 0;
foreach ($files as $file) {
    $name = basename($file, '.sql');
    if (in_array($name, $applied, true)) {
        continue;
    }

    // One change per file: a statement that changes a table commits on its own,
    // so a file with two can't be undone halfway.
    echo "Applying $name\n";
    $pdo->exec(file_get_contents($file));
    $record->execute([$name]);
    $count++;
}

echo $count === 0 ? "Nothing to do. The database is up to date.\n" : "Applied $count.\n";
