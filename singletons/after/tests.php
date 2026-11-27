<?php

declare(strict_types=1);

require __DIR__ . '/Mailer.php';
require __DIR__ . '/RecordingMailer.php';
require __DIR__ . '/Signup.php';

function check(string $name, bool $passed): void
{
    echo $passed ? 'ok    ' : 'FAIL  ', $name, "\n";
}

// Each test makes its own mailer, so nothing leaks from one to the next.
$mailer = new RecordingMailer();
(new Signup($mailer))->register('alice@example.com');
check('alice gets one welcome email', count($mailer->sent()) === 1);

$mailer = new RecordingMailer();
(new Signup($mailer))->register('bob@example.com');
check('bob gets one welcome email', count($mailer->sent()) === 1);
