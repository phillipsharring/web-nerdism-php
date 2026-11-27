<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

// Every page on the site: the path, the template, the title.
$routes = [
    '/' => ['home', 'Home'],
    '/about' => ['about', 'About'],
];

// Never trust input: the path is the visitor's. It's only ever looked up in
// $routes, never used to build a file name.
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = is_string($path) ? $path : '/';

if (isset($routes[$path])) {
    [$template, $title] = $routes[$path];
} else {
    http_response_code(404);
    [$template, $title] = ['not-found', 'Not Found'];
}

echo render('layout', [
    'config' => $config,
    'title' => $title,
    'path' => $path,
    'content' => render($template, ['config' => $config]),
]);
