<?php
declare(strict_types=1);

// Deletes login attempts older than --days (default 30). Meant to run from
// cron, so it says what it did in one line, keeps quiet otherwise, never runs
// twice at once, and exits non-zero when it fails.

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/src/db.php';

const BATCH = 1000;

/** One line for the log: when, in UTC, and what happened. */
function say(string $message, $stream = STDOUT): void
{
    fwrite($stream, gmdate('Y-m-d\TH:i:s\Z') . " $message\n");
}

$options = getopt('', ['days:', 'dry-run']);
$days = filter_var($options['days'] ?? 30, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$dryRun = isset($options['dry-run']);
if ($days === false) {
    say('--days takes a whole number, 1 or more.', STDERR);
    exit(2);
}

// One at a time. If the last run is still going, this one steps aside.
$lock = fopen(sys_get_temp_dir() . '/prune.lock', 'c');
if (!flock($lock, LOCK_EX | LOCK_NB)) {
    say('Already running; skipped.');
    exit(0);
}

try {
    $db = connect();
    $cutoff = gmdate('Y-m-d H:i:s', time() - $days * 86400);

    if ($dryRun) {
        $stmt = $db->prepare('SELECT COUNT(*) FROM login_attempts WHERE attempted_at < ?');
        $stmt->execute([$cutoff]);
        say("Dry run: would prune {$stmt->fetchColumn()} rows older than $days days.");
        exit(0);
    }

    // In batches, so no single DELETE holds the table for long.
    $delete = $db->prepare('DELETE FROM login_attempts WHERE attempted_at < ? ORDER BY id LIMIT ' . BATCH);
    $total = 0;
    do {
        $delete->execute([$cutoff]);
        $total += $delete->rowCount();
    } while ($delete->rowCount() === BATCH);

    say("Pruned $total rows older than $days days.");
    exit(0);
} catch (Throwable $e) {
    say('Failed: ' . $e->getMessage(), STDERR);
    exit(1);
}
