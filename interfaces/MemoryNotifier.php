<?php
declare(strict_types=1);

require_once __DIR__ . '/Notifier.php';

// Keeps each message instead of sending it. Two interfaces at once:
// it's a Notifier, and it's Countable, so count() works on it.
class MemoryNotifier implements Notifier, Countable
{
    private array $sent = [];

    public function send(string $to, string $message): void
    {
        $this->sent[] = ['to' => $to, 'message' => $message];
    }

    public function count(): int
    {
        return count($this->sent);
    }
}
