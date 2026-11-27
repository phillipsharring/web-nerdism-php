<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';

// Never trust input, and the command line is input too: a year, or nothing.
$before = filter_var($argv[1] ?? '', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 9999],
]);
if ($before === false) {
    fwrite(STDERR, "Usage: php books.php YEAR\n");
    exit(1);
}

$statement = $pdo->prepare('
    SELECT `title`, `author`, `year`
    FROM `books`
    WHERE `year` < ?
    ORDER BY `year`
');
$statement->execute([$before]);

foreach ($statement as $book) {
    echo "{$book['year']}  {$book['title']}, by {$book['author']}\n";
}
