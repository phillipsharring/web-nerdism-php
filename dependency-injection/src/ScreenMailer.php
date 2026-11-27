<?php
declare(strict_types=1);

namespace App;

// Prints the email instead of sending it. A real one would talk to a mail server.
final class ScreenMailer implements Mailer
{
    public function send(string $to, string $subject, string $body): void
    {
        echo "To: {$to}\nSubject: {$subject}\n\n{$body}\n\n";
    }
}
