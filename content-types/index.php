<?php
$types = [
    'html' => 'text/html',
    'text' => 'text/plain',
    'json' => 'application/json',
    'bytes' => 'application/octet-stream',
];
$as = $_GET['as'] ?? 'html';

header('Content-Type: ' . ($types[$as] ?? 'text/html'));
if (isset($_GET['save'])) {
    header('Content-Disposition: attachment; filename="hello.html"');
}

echo '<h1>Hello</h1><p>The same bytes, every time.</p>';
