<?php

declare(strict_types=1);

// The glue: read, decide, show. One line each; each lives in its own file.
require dirname(__DIR__) . '/src/data.php';
require dirname(__DIR__) . '/src/rules.php';

$posts = loadPosts(dirname(__DIR__, 2) . '/posts.json');
$posts = publishedNewestFirst($posts, new DateTimeImmutable('2026-11-27'));

require dirname(__DIR__) . '/views/posts.php';
