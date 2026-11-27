<?php
declare(strict_types=1);

// A pretend API, standing in for someone else's service. It wants a token,
// like most APIs. Like some, it also takes one in the query string, which is
// how the naive client gets into trouble.

header('Content-Type: application/json');
$expected = (string) getenv('API_TOKEN');
$sent = preg_match('/^Bearer (.+)$/', $_SERVER['HTTP_AUTHORIZATION'] ?? '', $m) ? $m[1] : (string) ($_GET['api_key'] ?? '');

if ($expected === '' || !hash_equals($expected, $sent)) {
    http_response_code(401);
    header('WWW-Authenticate: Bearer');
    exit(json_encode(['error' => 'Bad or missing token.']) . "\n");
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Busy: the first two calls in a row get "try again in a second".
if ($path === '/flaky') {
    $count = (int) @file_get_contents('/tmp/flaky') + 1;
    file_put_contents('/tmp/flaky', (string) ($count % 3));
    if ($count < 3) {
        http_response_code(503);
        header('Retry-After: 1');
        exit(json_encode(['error' => 'Busy. Try again.']) . "\n");
    }
    exit(json_encode(['ok' => true]) . "\n");
}

// Slow: longer than a client should wait.
if ($path === '/slow') {
    sleep(10);
    exit(json_encode(['ok' => true]) . "\n");
}

// Broken: says it's JSON, isn't.
if ($path === '/broken') {
    exit("<html>Oops</html>\n");
}

if ($path === '/tasks') {
    exit(json_encode(['tasks' => [
        ['id' => 1, 'title' => 'Write the API', 'done' => true],
        ['id' => 2, 'title' => 'Write the client', 'done' => false],
        ['id' => 3, 'title' => '<script>alert(1)</script>', 'done' => false],
    ]]) . "\n");
}

http_response_code(404);
echo json_encode(['error' => 'No such resource.']), "\n";
