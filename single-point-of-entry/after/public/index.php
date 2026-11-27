<?php

declare(strict_types=1);

// The single point of entry: every request starts here.

// A real file in public/, like a stylesheet: let PHP's built-in server send it.
// The path is input, so make sure it really is inside public/ first.
$file = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if (PHP_SAPI === 'cli-server' && $file !== false && str_starts_with($file, __DIR__ . '/') && is_file($file)) {
    return false;
}

require dirname(__DIR__) . '/src/app.php';
$routes = require dirname(__DIR__) . '/src/routes.php';

header('Content-Type: text/plain; charset=utf-8');

$route = $routes[parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)] ?? null;
$user = currentUser();

if ($route === null) {
    http_response_code(404);
    exit("Not found.\n");
}
// Admin pages don't admit they exist to anyone who isn't an admin.
if ($route['role'] === 'admin' && ($user['role'] ?? null) !== 'admin') {
    http_response_code(404);
    exit("Not found.\n");
}
if ($route['role'] !== null && $user === null) {
    http_response_code(401);
    exit("Log in first.\n");
}

require dirname(__DIR__) . "/pages/{$route['page']}.php";
