<?php

declare(strict_types=1);

// Knows about everything that happens after a signup, and does all of it.
final class Signup
{
    public function register(string $email, bool $wantsNewsletter): void
    {
        echo "Saved the account for $email\n";

        // A real app would call a mailer, a newsletter service and a log here.
        echo "  welcome email to $email\n";
        if ($wantsNewsletter) {
            echo "  added $email to the newsletter\n";
        }
        echo "  audit log: signup $email\n";
    }
}
