<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';

$perPage = 3;

// Never trust input: the page number is a whole number, 1 or more, or it's page 1.
$page = filter_var($argv[1] ?? '1', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($page === false) {
    $page = 1;
}

$statement = $pdo->prepare('
    SELECT `books`.`title`, `authors`.`name`, `books`.`rating`
    FROM `books`
    INNER JOIN `authors` ON `authors`.`id` = `books`.`author_id`
    ORDER BY `books`.`title`
    LIMIT ? OFFSET ?
');
$statement->bindValue(1, $perPage, PDO::PARAM_INT);
$statement->bindValue(2, ($page - 1) * $perPage, PDO::PARAM_INT);
$statement->execute();

echo "Page $page\n";
foreach ($statement as $book) {
    $rating = $book['rating'] === null ? 'not rated' : "{$book['rating']}/5";
    echo "  {$book['title']}, by {$book['name']} ($rating)\n";
}
