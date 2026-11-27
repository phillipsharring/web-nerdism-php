<?php

declare(strict_types=1);

// For tests: sends nothing, says nothing.
final class NullMailer implements Mailer
{
    public function send(string $to, string $subject): void
    {
    }
}
