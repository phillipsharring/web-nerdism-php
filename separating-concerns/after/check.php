<?php

declare(strict_types=1);

// The rules, checked on their own: no file, no server, no HTML.
require __DIR__ . '/src/rules.php';

$now = new DateTimeImmutable('2026-11-27');
$posts = [
    ['title' => 'Old', 'publishedOn' => new DateTimeImmutable('2026-01-01'), 'draft' => false],
    ['title' => 'Draft', 'publishedOn' => new DateTimeImmutable('2026-02-01'), 'draft' => true],
    ['title' => 'Future', 'publishedOn' => new DateTimeImmutable('2027-01-01'), 'draft' => false],
    ['title' => 'New', 'publishedOn' => new DateTimeImmutable('2026-11-01'), 'draft' => false],
];

$titles = array_column(publishedNewestFirst($posts, $now), 'title');
echo $titles === ['New', 'Old'] ? "ok\n" : 'FAIL: ' . implode(', ', $titles) . "\n";
