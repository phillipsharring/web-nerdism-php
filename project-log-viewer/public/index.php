<?php
declare(strict_types=1);

require dirname(__DIR__) . '/auth.php';
requireBasicAuth(require dirname(__DIR__) . '/users.php', 'Logs');
header('Cache-Control: private, no-store');

$logDir = dirname(__DIR__) . '/logs';

// The allow list: the .log files that are actually in the logs folder.
$logs = array_map('basename', glob($logDir . '/*.log'));
sort($logs);

// Never trust input. The name has to match one of ours exactly, or it's a 404.
$file = $_GET['file'] ?? null;
if ($file !== null && (!is_string($file) || !in_array($file, $logs, true))) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo "No such log.\n";
    exit;
}

// How many lines to show, from the end: a whole number from 1 to 1000.
$count = filter_var($_GET['lines'] ?? 100, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 1000, 'default' => 100],
]);

/**
 * The last $count lines of a file, without reading it all into memory.
 *
 * @return list<string>
 */
function lastLines(string $path, int $count): array
{
    $file = new SplFileObject($path);
    $file->seek(PHP_INT_MAX);              // jump past the end...
    $total = $file->key();                 // ...to learn how many lines there are
    $lines = [];
    for ($i = max(0, $total - $count); $i <= $total; $i++) {
        $file->seek($i);
        $line = rtrim((string) $file->current(), "\r\n");
        if ($line !== '') {
            $lines[] = $line;
        }
    }
    return array_slice($lines, -$count);    // the last line may or may not end in a newline
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Logs</title>
<h1>Logs</h1>
<ul>
    <?php foreach ($logs as $log): ?>
        <li><a href="?file=<?= urlencode($log) ?>"><?= htmlspecialchars($log) ?></a></li>
    <?php endforeach; ?>
</ul>
<?php if ($file !== null): ?>
    <h2><?= htmlspecialchars($file) ?>, last <?= $count ?> lines</h2>
    <?php // Log lines are input too: they hold whatever visitors sent. Escape them. ?>
    <pre><?php foreach (lastLines("$logDir/$file", $count) as $line): ?>
<?= htmlspecialchars($line) ?>
<?php endforeach; ?></pre>
<?php endif; ?>
