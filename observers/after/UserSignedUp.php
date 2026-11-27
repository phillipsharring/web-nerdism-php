<?php

declare(strict_types=1);

// The event: what happened, as a plain object. Nothing more.
final readonly class UserSignedUp
{
    public function __construct(public string $email, public bool $wantsNewsletter)
    {
    }
}
