<?php
declare(strict_types=1);

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

require dirname(__DIR__) . '/vendor/autoload.php';

$mailer = new Mailer(new EsmtpTransport(
    getenv('MAIL_HOST') ?: 'localhost',
    (int) (getenv('MAIL_PORT') ?: 1025),
));
