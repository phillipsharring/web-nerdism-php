<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

header('Content-Type: text/plain; charset=utf-8');

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($path === '/') {
    $logger->info('Home page viewed');
    echo "Hello. Every visit is written down in logs/app.log.\n";
} elseif ($path === '/search') {
    // Never trust input. It goes in the context, never into the message,
    // so the formatter can encode it safely.
    $q = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
    if ($q === '') {
        $logger->warning('Search with no query');
        http_response_code(400);
        echo "Search for something: /search?q=...\n";
    } else {
        $logger->info('Search', ['q' => $q]);
        echo 'You searched for: ', $q, "\n";
    }
} elseif ($path === '/boom') {
    throw new RuntimeException('The /boom page always fails, on purpose.');
} else {
    $logger->notice('Not found', ['path' => $path]);
    http_response_code(404);
    echo "Not found.\n";
}
