<?php
declare(strict_types=1);

// Runs jobs from the queue, one at a time, forever. Start as many of these as
// you like: each job still goes to only one of them.
//   --until-empty   stop when there's nothing left to do, instead of waiting

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Database;
use App\Queue;
use App\WelcomeEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

$untilEmpty = in_array('--until-empty', $argv, true);
$backoff = (int) (getenv('QUEUE_BACKOFF') === false ? 30 : getenv('QUEUE_BACKOFF')); // seconds, times the attempt number

$queue = new Queue((new Database())->connect());
$handlers = [
    'welcome-email' => new WelcomeEmail(new Mailer(Transport::fromDsn(getenv('MAILER_DSN')))),
];

function say(string $message): void
{
    echo gmdate('Y-m-d\TH:i:s\Z'), " $message\n";
}

say('Worker started.');
while (true) {
    $job = $queue->reserve();
    if ($job === null) {
        if ($untilEmpty) {
            break;
        }
        sleep(1);
        continue;
    }

    try {
        $handler = $handlers[$job['type']] ?? throw new RuntimeException("No handler for {$job['type']}");
        $handler($job['payload']);
        $queue->complete($job['id']);
        say("Job {$job['id']} ({$job['type']}) done.");
    } catch (Throwable $e) {
        // One bad job must never stop the worker. Note why, and try again later.
        $status = $queue->release($job['id'], $job['attempts'], $e->getMessage(), $backoff * $job['attempts']);
        say("Job {$job['id']} ({$job['type']}) failed, attempt {$job['attempts']}: {$e->getMessage()} Now $status.");
    }
}
say('Queue empty; stopping.');
