<?php

declare(strict_types=1);

require __DIR__ . '/src/load.php';

// The choosing, done by the factory, from the settings. A bad setting stops the app here.
$factory = new MailerFactory(getenv());
$mailer = $factory->create();

$mailer->send('alice@example.com', 'Welcome!');
