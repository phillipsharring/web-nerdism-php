<?php
declare(strict_types=1);

require_once __DIR__ . '/Notifier.php';

// Prints each message. Handy while you're building.
class ScreenNotifier implements Notifier
{
    public function send(string $to, string $message): void
    {
        echo "To {$to}: {$message}\n";
    }
}
