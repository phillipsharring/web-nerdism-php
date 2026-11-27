<?php
// Sends a stored upload back, as an image and nothing else.
declare(strict_types=1);

require __DIR__ . '/uploads.php';

// Never trust input: the id must look exactly like one saveUpload() made,
// so it can't be ../../something.
$id = $_GET['id'] ?? '';
if (!is_string($id) || !preg_match('/\A[0-9a-f]{32}\.(png|jpg|gif)\z/', $id, $match)
    || !is_file(STORAGE . '/' . $id)) {
    http_response_code(404);
    exit('Not found.');
}

header('Content-Type: ' . array_search($match[1], TYPES, true));
header('X-Content-Type-Options: nosniff'); // don't guess; it's an image
header('Content-Length: ' . filesize(STORAGE . '/' . $id));
readfile(STORAGE . '/' . $id);
