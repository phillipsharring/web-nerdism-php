<?php

declare(strict_types=1);

// For development: writes the email down instead of sending it.
final class LogMailer implements Mailer
{
    public function send(string $to, string $subject): void
    {
        echo "[log] To $to: $subject\n";
    }
}
