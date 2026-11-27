<?php

declare(strict_types=1);

require __DIR__ . '/src/load.php';

// The choosing, done inline, wherever a mailer is needed.
$kind = getenv('MAILER');
if ($kind === 'smtp') {
    $mailer = new SmtpMailer(getenv('SMTP_HOST') ?: 'localhost', (int) getenv('SMTP_PORT'));
} elseif ($kind === 'none') {
    $mailer = new NullMailer();
} else {
    $mailer = new LogMailer();
}

$mailer->send('alice@example.com', 'Welcome!');
