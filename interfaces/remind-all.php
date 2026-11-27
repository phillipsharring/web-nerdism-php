<?php
declare(strict_types=1);

require_once __DIR__ . '/Notifier.php';

// Takes any Notifier. It doesn't know or care which kind.
function remindAll(Notifier $notifier, array $emails): void
{
    foreach ($emails as $email) {
        $notifier->send($email, 'Your trial ends tomorrow.');
    }
}
