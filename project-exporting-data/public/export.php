<?php
declare(strict_types=1);

require dirname(__DIR__) . '/auth.php';
require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/csv.php';

// Customer data: staff only.
requireBasicAuth(require dirname(__DIR__) . '/users.php', 'Export');

// ?since=2026-11-10, optional. Never trust input: it must be a real date in
// exactly that shape, or it's a 400.
$since = null;
if (isset($_GET['since']) && $_GET['since'] !== '') {
    $date = is_string($_GET['since']) ? DateTimeImmutable::createFromFormat('!Y-m-d', $_GET['since']) : false;
    if ($date === false || $date->format('Y-m-d') !== $_GET['since']) {
        http_response_code(400);
        header('Content-Type: text/plain; charset=utf-8');
        exit("since must be a date like 2026-11-10.\n");
    }
    $since = $date;
}

$db = connect();
// Stream rows from MySQL one at a time instead of loading them all first.
$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
$stmt = $db->prepare(
    'SELECT name, email, company, spent_cents, created_at FROM contacts
      WHERE created_at >= ? ORDER BY created_at'
);
$stmt->execute([$since?->format('Y-m-d 00:00:00') ?? '1000-01-01 00:00:00']);

// The download: a CSV, saved under a name we choose, never cached.
$filename = 'contacts' . ($since ? '-since-' . $since->format('Y-m-d') : '') . '-' . gmdate('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');

$out = fopen('php://output', 'w');
fwrite($out, "\u{FEFF}"); // a byte order mark, so Excel reads the file as UTF-8
writeRow($out, ['Name', 'Email', 'Company', 'Spent', 'Signed up']);
foreach ($stmt as $row) {
    writeRow($out, [
        $row['name'],
        $row['email'],
        $row['company'],
        sprintf('%.2f', $row['spent_cents'] / 100), // fine for showing; the math stayed in cents
        $row['created_at'],
    ]);
}
fclose($out);
