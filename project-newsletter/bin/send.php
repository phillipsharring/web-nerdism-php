<?php
declare(strict_types=1);

// Sends one issue to every confirmed subscriber, and nobody else.
//   php bin/send.php issues/2026-11.txt [--dry-run]
// The first line of the file is the subject; the rest is the body.

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/src/services.php';

use Symfony\Component\Mime\Email;

$file = $argv[1] ?? '';
$dryRun = in_array('--dry-run', $argv, true);
if (!is_file($file)) {
    fwrite(STDERR, "Usage: php bin/send.php ISSUE-FILE [--dry-run]\n");
    exit(2);
}
[$subject, $body] = explode("\n", trim(file_get_contents($file)), 2) + [1 => ''];

$recipients = $db->query("SELECT id, email FROM subscribers WHERE status = 'confirmed' ORDER BY id")->fetchAll();
if ($dryRun) {
    echo 'Dry run: would send "', $subject, '" to ', count($recipients), " subscriber(s).\n";
    exit(0);
}

foreach ($recipients as $r) {
    $unsubscribe = $subscriptions->unsubscribeUrl((int) $r['id']);
    $email = (new Email())
        ->from('newsletter@example.com')
        ->to($r['email'])
        ->subject($subject)
        ->text(trim($body) . "\n\nYou're getting this because you subscribed and confirmed.\nStop any time: $unsubscribe\n");
    // The headers mail programs use for their own Unsubscribe button (RFC 2369
    // and RFC 8058). Gmail and Yahoo require them of bulk senders.
    $email->getHeaders()->addTextHeader('List-Unsubscribe', "<$unsubscribe>");
    $email->getHeaders()->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
    $mailer->send($email);
}
echo 'Sent "', $subject, '" to ', count($recipients), " subscriber(s).\n";
