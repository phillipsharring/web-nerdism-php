<?php
declare(strict_types=1);

// The other sites whose pages may read this API's answers in a browser.
// An allow list, exact: scheme, host and port.
$allowed = ['http://localhost:8001'];

// Origin is a header, and headers are input. It only decides what this
// answer says to browsers; it never decides who gets in.
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST');
    header('Access-Control-Allow-Headers: Content-Type');
}
// The answer depends on Origin, so any cache must keep one copy per Origin.
header('Vary: Origin');

// A preflight: the browser asking first. The headers above are the answer.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

header('Content-Type: application/json');
echo json_encode(['message' => 'Hello from the API.'], JSON_THROW_ON_ERROR);
