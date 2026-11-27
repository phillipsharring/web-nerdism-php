<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';

// Never trust input. An author id has to be a number, and then a real author.
$authorId = filter_var($argv[1] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$title = trim($argv[2] ?? '');
$year = filter_var($argv[3] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 9999]]);

if ($authorId === false || $title === '' || mb_strlen($title) > 200 || $year === false) {
    fwrite(STDERR, "Usage: php add-book.php AUTHOR_ID TITLE YEAR\n");
    exit(1);
}

$statement = $pdo->prepare('
    INSERT INTO `books` (`author_id`, `title`, `year`)
    VALUES (?, ?, ?)
');

try {
    $statement->execute([$authorId, $title, $year]);
} catch (PDOException $e) {
    // 1452: the foreign key found no such author. Anything else is a real problem.
    if (($e->errorInfo[1] ?? null) === 1452) {
        fwrite(STDERR, "There's no author #$authorId.\n");
        exit(1);
    }
    throw $e;
}

echo 'Added book #', $pdo->lastInsertId(), "\n";
