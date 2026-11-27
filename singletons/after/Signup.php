<?php

declare(strict_types=1);

final class Signup
{
    // It says what it needs, and whoever makes a Signup hands it over.
    public function __construct(private Mailer $mailer)
    {
    }

    public function register(string $email): void
    {
        // ...save the account, then:
        $this->mailer->send($email, 'Welcome!');
    }
}
