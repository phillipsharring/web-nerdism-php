<?php
declare(strict_types=1);

// Builds the objects every page needs, from settings in the environment.

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Database;
use App\Subscriptions;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

$db = (new Database())->connect();
$mailer = new Mailer(Transport::fromDsn(getenv('MAILER_DSN')));
$subscriptions = new Subscriptions($db, $mailer, rtrim(getenv('SITE_URL'), '/'), getenv('NEWSLETTER_SECRET'));
