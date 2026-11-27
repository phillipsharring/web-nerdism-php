<?php

declare(strict_types=1);

// Keeps notes in a JSON file, so the example needs no database.
final class NoteStore
{
    public function __construct(private string $file)
    {
        if (!is_file($file)) {
            $this->save([1 => 'Order more oat milk', 2 => 'Fix the café sign']);
        }
    }

    /** @return array<int, string> */
    public function all(): array
    {
        $notes = [];
        foreach (json_decode(file_get_contents($this->file), true, flags: JSON_THROW_ON_ERROR) as $id => $body) {
            $notes[(int) $id] = $body;
        }
        return $notes;
    }

    /** @param array<int, string> $notes */
    public function save(array $notes): void
    {
        file_put_contents($this->file, json_encode($notes, JSON_THROW_ON_ERROR), LOCK_EX);
    }
}
