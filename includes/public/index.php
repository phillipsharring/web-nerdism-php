<?php
declare(strict_types=1);

// Only this file is in public/. Everything it includes is out of reach of a URL.
$root = dirname(__DIR__);

$config = require $root . '/config.php'; // a file can return a value
require_once $root . '/functions.php';   // _once: functions can't be defined twice

// Never trust input. The page name comes from the query string, so it has to be
// one of ours. An include path is never built from what a visitor sent.
$pages = ['home', 'about'];
$page = $_GET['page'] ?? 'home';
if (!in_array($page, $pages, true)) {
    http_response_code(404);
    $page = 'not-found';
}

// Included files share this scope: they can see $config and $page.
require $root . '/templates/header.php';
require $root . '/pages/' . $page . '.php';
require $root . '/templates/footer.php';
