<?php

declare(strict_types=1);

require __DIR__ . '/Mailer.php';
require __DIR__ . '/RecordingMailer.php';
require __DIR__ . '/Signup.php';

// The one place that builds the app. One mailer, made here, handed to everything that needs it.
$mailer = new RecordingMailer();
$signup = new Signup($mailer);

$signup->register('alice@example.com');
$signup->register('bob@example.com');

echo count($mailer->sent()), " emails, one mailer:\n", implode("\n", $mailer->sent()), "\n";
