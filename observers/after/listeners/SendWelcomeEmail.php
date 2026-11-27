<?php

declare(strict_types=1);

final class SendWelcomeEmail
{
    public function __invoke(UserSignedUp $event): void
    {
        echo "  welcome email to $event->email\n";
    }
}
