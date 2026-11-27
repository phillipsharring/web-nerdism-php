<?php

declare(strict_types=1);

// Saves the account and announces it. It doesn't know who's listening.
final class Signup
{
    public function __construct(private EventDispatcher $events)
    {
    }

    public function register(string $email, bool $wantsNewsletter): void
    {
        echo "Saved the account for $email\n";

        $this->events->dispatch(new UserSignedUp($email, $wantsNewsletter));
    }
}
