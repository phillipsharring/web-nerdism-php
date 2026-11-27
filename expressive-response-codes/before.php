<?php

declare(strict_types=1);

// Expressive to everyone, attackers included. Don't ship this one.
require __DIR__ . '/app.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/login' && $method === 'POST') {
    $id = userIdByEmail(postedString('email'));
    if ($id === null) {
        respond(404, ['error' => 'No account with that email.']);
    }
    if (!password_verify(postedString('password'), USERS[$id]['hash'])) {
        respond(401, ['error' => 'Wrong password.']);
    }
    respond(200, ['token' => array_search($id, TOKENS, true)]);
}

if (preg_match('#^/notes/(\d+)$#', $path, $m) && $method === 'GET') {
    $userId = currentUserId();
    if ($userId === null) {
        respond(401, ['error' => 'Log in first.']);
    }
    $note = NOTES[(int) $m[1]] ?? null;
    if ($note === null) {
        respond(404, ['error' => 'No such note.']);
    }
    if ($note['owner'] !== $userId) {
        respond(403, ['error' => 'That note belongs to someone else.']);
    }
    respond(200, ['text' => $note['text']]);
}

respond(404, ['error' => 'Not found.']);
