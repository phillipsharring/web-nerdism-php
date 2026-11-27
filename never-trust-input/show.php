<?php
// Shows everything this request brought with it. Every line below is input:
// the person (or program) on the other end chose all of it.
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$input = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'path' => $_SERVER['REQUEST_URI'],
    'query string ($_GET)' => $_GET,
    'form fields ($_POST)' => $_POST,
    'cookies ($_COOKIE)' => $_COOKIE,
    'User-Agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
    'Referer' => $_SERVER['HTTP_REFERER'] ?? null,
    'Host' => $_SERVER['HTTP_HOST'] ?? null,
    'X-Forwarded-For' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
];

// Invalid UTF-8 is input too: it shows up as the replacement character, �.
echo json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE), "\n";
