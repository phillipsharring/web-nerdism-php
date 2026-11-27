<?php
declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/** The job: send one welcome email. */
final class WelcomeEmail
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function __invoke(array $payload): void
    {
        // The payload came out of our database, but it went in from a form.
        // Never trust input: check it again here, where it's used.
        $to = $payload['email'] ?? null;
        if (!is_string($to) || filter_var($to, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('No valid address in the payload.');
        }

        $this->mailer->send(
            (new Email())
                ->from('hello@example.com')
                ->to($to)
                ->subject('Welcome!')
                ->text("Thanks for signing up.\n")
        );
    }
}
