<?php

declare(strict_types=1);

require __DIR__ . '/UserSignedUp.php';
require __DIR__ . '/EventDispatcher.php';
require __DIR__ . '/Signup.php';
require __DIR__ . '/listeners/SendWelcomeEmail.php';
require __DIR__ . '/listeners/AddToNewsletter.php';
require __DIR__ . '/listeners/WriteAuditLog.php';

// The wiring: who listens to what, in one place.
$events = new EventDispatcher();
$events->listen(UserSignedUp::class, new SendWelcomeEmail());
$events->listen(UserSignedUp::class, new AddToNewsletter());
$events->listen(UserSignedUp::class, new WriteAuditLog());

// Added later, with no change to Signup.php at all.
$events->listen(UserSignedUp::class, function (UserSignedUp $event): void {
    echo "  told the team: $event->email signed up\n";
});

$signup = new Signup($events);
$signup->register('alice@example.com', true);
$signup->register('bob@example.com', false);
