<?php

declare(strict_types=1);

// Reading: where posts come from, and nothing else.
// A file is input like any other, so check its shape before anything uses it.
function loadPosts(string $file): array
{
    $rows = json_decode(file_get_contents($file), true, flags: JSON_THROW_ON_ERROR);
    $posts = [];
    foreach ($rows as $row) {
        if (!is_string($row['title'] ?? null) || !is_string($row['publishedOn'] ?? null)) {
            continue;
        }
        $posts[] = [
            'title' => $row['title'],
            'publishedOn' => new DateTimeImmutable($row['publishedOn']),
            'draft' => ($row['draft'] ?? true) !== false,
        ];
    }
    return $posts;
}
