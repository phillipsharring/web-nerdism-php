<?php

declare(strict_types=1);

use App\Note;
use App\NoteMapper;
use App\NotesTable;

require __DIR__ . '/vendor/autoload.php';

$db = require __DIR__ . '/config/db.php';
$table = new NotesTable($db);
$mapper = new NoteMapper();

// Create: a Note becomes a row, and the row goes in the table.
$note = new Note(null, 'Order more oat milk ☕', new DateTimeImmutable('2026-11-27 09:00'));
$id = $table->insert($mapper->toRow($note));

// Read: a row comes out of the table and becomes a Note again.
$saved = $mapper->toModel($table->find($id));
echo "Saved #$saved->id: $saved->body, at {$saved->createdAt->format('g:i a')}\n";

// Update: change the Note, then save its row.
$table->update($id, $mapper->toRow($saved->withBody('Order oat milk today ☕')));
echo 'Now: ', $mapper->toModel($table->find($id))->body, "\n";

// The model's rules hold everywhere, even for a change that never reaches the database.
try {
    $saved->withBody('   ');
} catch (InvalidArgumentException $e) {
    echo 'Refused: ', $e->getMessage(), "\n";
}

// A row from the database is input too. One that breaks the rules doesn't become a Note.
$db->exec("INSERT INTO notes (body, created_at) VALUES ('', '2026-11-27 10:00:00')");
foreach ($table->all() as $row) {
    try {
        $note = $mapper->toModel($row);
        echo "Listed #$note->id: $note->body\n";
    } catch (InvalidArgumentException $e) {
        echo "Skipped row #{$row['id']}: ", $e->getMessage(), "\n";
    }
}

// Delete.
$table->delete($id);
echo 'After delete: ', count($table->all()), " row left\n";
