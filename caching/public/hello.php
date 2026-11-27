<?php
declare(strict_types=1);

// A page about one person. No cache anywhere may keep it: not a CDN, not a
// proxy, not even the browser's own disk.
header('Cache-Control: private, no-store');
header('Content-Type: text/html; charset=utf-8');

// Never trust input. The name comes from the query string, so check it, and
// escape it on the way out.
$name = $_GET['name'] ?? '';
if (!is_string($name) || $name === '' || mb_strlen($name) > 40) {
    $name = 'stranger';
}

echo '<p>Hello, ' . htmlspecialchars($name) . ".</p>\n";
