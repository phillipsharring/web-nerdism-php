<?php

declare(strict_types=1);

// Reading, deciding and showing, all tangled together.
$now = new DateTimeImmutable('2026-11-27');
?>
<!doctype html>
<meta charset="utf-8">
<title>Recent posts</title>
<h1>Recent posts</h1>
<ul>
<?php
$posts = json_decode(file_get_contents(__DIR__ . '/../posts.json'), true);
usort($posts, fn($a, $b) => strcmp($b['publishedOn'], $a['publishedOn']));
foreach ($posts as $post) {
    if (!$post['draft'] && new DateTimeImmutable($post['publishedOn']) <= $now) {
        echo '<li>', htmlspecialchars($post['title']), ' <small>',
            (new DateTimeImmutable($post['publishedOn']))->format('M j'), '</small></li>', "\n";
    }
}
?>
</ul>
