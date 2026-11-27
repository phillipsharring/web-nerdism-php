<?php

declare(strict_types=1);

// Showing: HTML only. It gets $posts and escapes everything it prints.
?>
<!doctype html>
<meta charset="utf-8">
<title>Recent posts</title>
<h1>Recent posts</h1>
<ul>
<?php foreach ($posts as $post): ?>
<li><?= htmlspecialchars($post['title']) ?> <small><?= $post['publishedOn']->format('M j') ?></small></li>
<?php endforeach; ?>
</ul>
