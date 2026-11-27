<?php
declare(strict_types=1);

// Every query here asks for published posts only: a publish time that has
// arrived. Drafts and scheduled posts are filtered out in SQL, in one place.
const PUBLISHED = 'published_at IS NOT NULL AND published_at <= UTC_TIMESTAMP()';

/** @return list<array<string, string>> newest first */
function latestPosts(PDO $db, int $limit, int $offset): array
{
    $stmt = $db->prepare(
        'SELECT slug, title, body, published_at FROM posts WHERE ' . PUBLISHED .
        ' ORDER BY published_at DESC, id DESC LIMIT :limit OFFSET :offset'
    );
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function countPosts(PDO $db): int
{
    return (int) $db->query('SELECT COUNT(*) FROM posts WHERE ' . PUBLISHED)->fetchColumn();
}

/** @return array<string, string>|null */
function findPost(PDO $db, string $slug): ?array
{
    $stmt = $db->prepare('SELECT slug, title, body, published_at, updated_at FROM posts WHERE slug = ? AND ' . PUBLISHED);
    $stmt->execute([$slug]);

    return $stmt->fetch() ?: null;
}

/** Plain text to paragraphs: a blank line starts a new one. @return list<string> */
function paragraphs(string $body): array
{
    return preg_split('/\R{2,}/', trim($body));
}

/** A UTC time from the database, written for people. */
function niceDate(string $utc): string
{
    return (new DateTimeImmutable($utc, new DateTimeZone('UTC')))->format('F j, Y');
}

/** The same time for machines: RFC 3339, which the Atom feed needs. */
function feedDate(string $utc): string
{
    return (new DateTimeImmutable($utc, new DateTimeZone('UTC')))->format(DATE_ATOM);
}
