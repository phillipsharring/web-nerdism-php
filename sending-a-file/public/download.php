<?php
declare(strict_types=1);

// The files you're willing to send: a short name for the URL, the file on
// disk (in files/, outside the web root), and the name to save it as.
$files = [
    'report' => ['path' => 'report.csv', 'name' => 'sales-report.csv'],
    'menu' => ['path' => 'menu.csv', 'name' => 'café-menu.csv'],
    'big' => ['path' => 'big.csv', 'name' => 'everyone.csv'],
];

// Never trust input: ?file= picks from the list above, and nothing else.
$key = $_GET['file'] ?? '';
$file = is_string($key) ? ($files[$key] ?? null) : null;
$path = $file === null ? '' : dirname(__DIR__) . '/files/' . $file['path'];

if ($file === null || !is_file($path)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo "No such file.\n";
    exit;
}

// A plain ASCII name for old browsers, then the real one, UTF-8 and percent-encoded.
$ascii = preg_replace('/[^A-Za-z0-9._-]/u', '_', $file['name']);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $ascii . '"; filename*=UTF-8\'\'' . rawurlencode($file['name']));
header('Content-Length: ' . filesize($path));

// Close any open output buffers, so the file goes straight out instead of piling up in memory.
while (ob_get_level() > 0) {
    ob_end_clean();
}

readfile($path); // Reads and sends a piece at a time.
