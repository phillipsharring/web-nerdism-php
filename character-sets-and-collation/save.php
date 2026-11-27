<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';

// Never trust input: the text has to be valid UTF-8, and not too long.
$text = $argv[1] ?? '';
if ($text === '' || !mb_check_encoding($text, 'UTF-8') || mb_strlen($text) > 200) {
    fwrite(STDERR, "Usage: php save.php TEXT (UTF-8, up to 200 characters)\n");
    exit(1);
}

// The same text, into both tables.
foreach (['notes_utf8', 'notes'] as $table) {
    $statement = $pdo->prepare("
        INSERT INTO `$table` (`body`)
        VALUES (?)
    ");
    try {
        $statement->execute([$text]);
        echo "$table: saved\n";
    } catch (PDOException $e) {
        echo "$table: refused. {$e->errorInfo[2]}\n";
    }
}
