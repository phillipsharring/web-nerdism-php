<?php

declare(strict_types=1);

namespace App;

use PDO;

// The table: SQL for the notes table, and nothing else. Rows in, rows out.
final class NotesTable
{
    public function __construct(private PDO $db)
    {
    }

    public function insert(array $row): int
    {
        $this->db->prepare('INSERT INTO notes (body, created_at) VALUES (:body, :created_at)')
            ->execute(['body' => $row['body'], 'created_at' => $row['created_at']]);
        return (int) $this->db->lastInsertId();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT id, body, created_at FROM notes WHERE id = ?');
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, body, created_at FROM notes ORDER BY id')->fetchAll();
    }

    public function update(int $id, array $row): void
    {
        $this->db->prepare('UPDATE notes SET body = :body, created_at = :created_at WHERE id = :id')
            ->execute(['body' => $row['body'], 'created_at' => $row['created_at'], 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->db->prepare('DELETE FROM notes WHERE id = ?')->execute([$id]);
    }
}
