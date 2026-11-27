<?php
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';

// Never trust input.
$id = filter_var($argv[1] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    fwrite(STDERR, "Usage: php delete-post.php POST_ID\n");
    exit(1);
}

$delete = $pdo->prepare('
    DELETE FROM `posts`
    WHERE `id` = ?
');
$delete->execute([$id]);

// rowCount() counts the posts. The comments the cascade removed aren't included.
echo 'Posts deleted: ', $delete->rowCount(), "\n";
$left = $pdo->query('
    SELECT COUNT(*)
    FROM `comments`
')->fetchColumn();
echo "Comments left: $left\n";
