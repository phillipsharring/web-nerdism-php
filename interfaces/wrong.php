<?php
declare(strict_types=1);

require __DIR__ . '/remind-all.php';

// It has a send() method just like a Notifier's, but it never says it is one.
class Pager
{
    public function send(string $to, string $message): void
    {
        echo "Beep {$to}\n";
    }
}

remindAll(new Pager(), ['ada@example.com']);
