<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/uploads.php';

// Uploaded images live outside the web root, in storage/uploads. This script
// is the only way to reach them, and it only ever sends them as images.
$name = $_GET['name'] ?? '';
if (!is_string($name) || !preg_match('/^[a-f0-9]{32}\.(png|jpg|webp)$/', $name, $m)) {
    http_response_code(404);
    exit;
}
$path = dirname(__DIR__) . "/storage/uploads/$name";
if (!is_file($path)) {
    http_response_code(404);
    exit;
}

header('Content-Type: ' . array_search($m[1], IMAGE_TYPES, true));
header('X-Content-Type-Options: nosniff');   // browsers: believe the type, don't guess
header('Content-Length: ' . filesize($path));
header('Cache-Control: public, max-age=31536000, immutable'); // a new image gets a new name
readfile($path);
