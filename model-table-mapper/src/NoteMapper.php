<?php

declare(strict_types=1);

namespace App;

use DateTimeImmutable;

// The mapper: turns a row into a Note and a Note into a row. No SQL, no rules of its own.
final class NoteMapper
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    public function toModel(array $row): Note
    {
        return new Note(
            (int) $row['id'],
            $row['body'],
            DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $row['created_at']),
        );
    }

    public function toRow(Note $note): array
    {
        return [
            'body' => $note->body,
            'created_at' => $note->createdAt->format(self::DATE_FORMAT),
        ];
    }
}
