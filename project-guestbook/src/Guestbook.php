<?php
declare(strict_types=1);

// Entries are stored one per line, as JSON, in a file outside public/.
// Stored as they were typed: escaping happens on the way out, not in here.
final class Guestbook
{
    public function __construct(private string $file)
    {
    }

    /** @param array{name: string, website: string, message: string} $entry */
    public function add(array $entry): void
    {
        if (!is_dir(dirname($this->file))) {
            mkdir(dirname($this->file), 0700, true);
        }
        $entry['at'] = date('c');
        file_put_contents($this->file, json_encode($entry) . "\n", FILE_APPEND | LOCK_EX);
    }

    /** @return list<array{name: string, website: string, message: string, at: string}> newest first */
    public function latest(int $count = 50): array
    {
        if (!is_file($this->file)) {
            return [];
        }
        $lines = file($this->file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $entries = array_map(fn (string $line): array => json_decode($line, true), $lines);

        return array_slice(array_reverse($entries), 0, $count);
    }
}
