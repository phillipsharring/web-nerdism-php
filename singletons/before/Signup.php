<?php

declare(strict_types=1);

final class Signup
{
    public function register(string $email): void
    {
        // ...save the account, then:
        Mailer::getInstance()->send($email, 'Welcome!');
    }
}
