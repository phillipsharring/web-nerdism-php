<?php
declare(strict_types=1);

namespace App;

// What the rest of the code needs from a mailer, and nothing more.
interface Mailer
{
    public function send(string $to, string $subject, string $body): void;
}
