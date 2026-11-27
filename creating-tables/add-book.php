<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';

// The formats live here, not in the table. Adding one is a one-line change.
$formats = ['hardcover', 'paperback', 'ebook'];

// Never trust input: check every argument before the database sees it.
$title = trim($argv[1] ?? '');
$format = $argv[2] ?? '';
$price = filter_var($argv[3] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

$errors = [];
if ($title === '' || mb_strlen($title) > 200) {
    $errors[] = 'A title, up to 200 characters.';
}
if (!in_array($format, $formats, true)) {
    $errors[] = 'A format: ' . implode(', ', $formats) . '.';
}
if ($price === false) {
    $errors[] = 'A price in cents: 0 or more, no decimal point.';
}
if ($errors) {
    fwrite(STDERR, "Usage: php add-book.php TITLE FORMAT PRICE_CENTS\n" . implode("\n", $errors) . "\n");
    exit(1);
}

$statement = $pdo->prepare('
    INSERT INTO `books` (`title`, `format`, `price_cents`)
    VALUES (?, ?, ?)
');
$statement->execute([$title, $format, $price]);

echo 'Added book #', $pdo->lastInsertId(), "\n";
