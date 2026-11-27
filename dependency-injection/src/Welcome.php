<?php
declare(strict_types=1);

namespace App;

use InvalidArgumentException;

// Sends the welcome email. It needs a Mailer, and says so, in its constructor.
// It doesn't make one, doesn't look one up, and doesn't know which kind it gets.
final class Welcome
{
    public function __construct(private Mailer $mailer)
    {
    }

    public function send(string $email, string $name): void
    {
        // Never trust input: the address and name came from a signup form.
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('That is not an email address.');
        }
        $name = trim(preg_replace('/[\r\n]+/', ' ', $name));

        $this->mailer->send($email, 'Welcome to Example', "Hi {$name}, thanks for signing up.");
    }
}
