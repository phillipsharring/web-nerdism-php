<?php

declare(strict_types=1);

// The four things you can do to a note. Each one is one SQL statement.

function createNote(PDO $db, string $body): int
{
    $db->prepare('INSERT INTO notes (body) VALUES (?)')->execute([$body]);
    return (int) $db->lastInsertId();
}

function allNotes(PDO $db): array
{
    return $db->query('SELECT id, body FROM notes ORDER BY id')->fetchAll();
}

function findNote(PDO $db, int $id): ?array
{
    $statement = $db->prepare('SELECT id, body FROM notes WHERE id = ?');
    $statement->execute([$id]);
    return $statement->fetch() ?: null;
}

function updateNote(PDO $db, int $id, string $body): void
{
    $db->prepare('UPDATE notes SET body = ? WHERE id = ?')->execute([$body, $id]);
}

function deleteNote(PDO $db, int $id): void
{
    $db->prepare('DELETE FROM notes WHERE id = ?')->execute([$id]);
}

// Never trust input: a note is a string of 1 to 500 characters, or it's an error.
function noteBodyError(mixed $body): ?string
{
    if (!is_string($body) || trim($body) === '') {
        return 'A note needs some text.';
    }
    if (mb_strlen($body) > 500) {
        return 'A note is 500 characters at most.';
    }
    return null;
}
