<?php
declare(strict_types=1);

namespace App;

// Keeps each email in a list. Nothing is sent, and you can look at what would have been.
final class MemoryMailer implements Mailer
{
    /** @var list<array{to: string, subject: string, body: string}> */
    public array $sent = [];

    public function send(string $to, string $subject, string $body): void
    {
        $this->sent[] = ['to' => $to, 'subject' => $subject, 'body' => $body];
    }
}
