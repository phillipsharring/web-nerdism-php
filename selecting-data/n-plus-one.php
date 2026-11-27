<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';

// The N+1 way: one query for the authors, then one more for each author's books.
$queries = 0;

$authors = $pdo->query('
    SELECT `id`, `name`
    FROM `authors`
    ORDER BY `name`
')->fetchAll();
$queries++;

$books = $pdo->prepare('
    SELECT `title`
    FROM `books`
    WHERE `author_id` = ?
    ORDER BY `year`
');
foreach ($authors as $author) {
    $books->execute([$author['id']]);
    $queries++;
    $titles = $books->fetchAll(PDO::FETCH_COLUMN);
    echo $author['name'], ': ', $titles ? implode(', ', $titles) : '(none)', "\n";
}
echo "The N+1 way: $queries queries\n\n";

// The JOIN way: one query, and PHP sorts the rows into authors.
$queries = 0;

$rows = $pdo->query('
    SELECT `authors`.`name`, `books`.`title`
    FROM `authors`
    LEFT JOIN `books` ON `books`.`author_id` = `authors`.`id`
    ORDER BY `authors`.`name`, `books`.`year`
')->fetchAll();
$queries++;

$byAuthor = [];
foreach ($rows as $row) {
    $byAuthor[$row['name']] ??= [];
    if ($row['title'] !== null) {
        $byAuthor[$row['name']][] = $row['title'];
    }
}
foreach ($byAuthor as $name => $titles) {
    echo $name, ': ', $titles ? implode(', ', $titles) : '(none)', "\n";
}
echo "The JOIN way: $queries query\n";
