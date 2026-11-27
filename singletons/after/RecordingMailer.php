<?php

declare(strict_types=1);

// A plain class. Make as many as you like; the app makes one.
final class RecordingMailer implements Mailer
{
    /** @var string[] */
    private array $sent = [];

    public function send(string $to, string $subject): void
    {
        $this->sent[] = "$to: $subject";
    }

    /** @return string[] */
    public function sent(): array
    {
        return $this->sent;
    }
}
