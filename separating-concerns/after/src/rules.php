<?php

declare(strict_types=1);

// Deciding: which posts to show, in what order. No files, no HTML.
function publishedNewestFirst(array $posts, DateTimeImmutable $now): array
{
    $published = array_filter(
        $posts,
        fn(array $post) => !$post['draft'] && $post['publishedOn'] <= $now,
    );
    usort($published, fn(array $a, array $b) => $b['publishedOn'] <=> $a['publishedOn']);

    return $published;
}
