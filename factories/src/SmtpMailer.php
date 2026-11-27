<?php

declare(strict_types=1);

// For production. This one only pretends, so the example needs no mail server.
final class SmtpMailer implements Mailer
{
    public function __construct(private string $host, private int $port)
    {
    }

    public function send(string $to, string $subject): void
    {
        echo "[smtp {$this->host}:{$this->port}] To $to: $subject\n";
    }
}
