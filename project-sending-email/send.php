<?php
declare(strict_types=1);

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

require __DIR__ . '/vendor/autoload.php';

// Never trust input, even from the command line: where an address came from
// today isn't where it will come from tomorrow.
$to = $argv[1] ?? '';
if (filter_var($to, FILTER_VALIDATE_EMAIL) === false) {
    fwrite(STDERR, "Usage: php send.php someone@example.com\n");
    exit(1);
}

// Where to send it comes from the environment: Mailpit here, a real mail
// service on a live site.
$transport = new EsmtpTransport(getenv('MAIL_HOST') ?: 'localhost', (int) (getenv('MAIL_PORT') ?: 1025));
$mailer = new Mailer($transport);

$email = (new Email())
    ->from(new Address('hello@example.com', 'Example Site'))
    ->to($to)
    ->subject('Welcome to the café')
    ->text("Hello!\n\nThis came from PHP, by way of Mailpit. Nothing left your computer.\n")
    ->html('<p>Hello!</p><p>This came from <strong>PHP</strong>, by way of Mailpit. Nothing left your computer.</p>');

$mailer->send($email);

echo "Sent to $to. Read it at http://localhost:8025\n";
