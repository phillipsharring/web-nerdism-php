<?php

declare(strict_types=1);

final class AddToNewsletter
{
    public function __invoke(UserSignedUp $event): void
    {
        if ($event->wantsNewsletter) {
            echo "  added $event->email to the newsletter\n";
        }
    }
}
