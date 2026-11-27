<?php

declare(strict_types=1);

// Expressive to your users, vague to your attackers.
require __DIR__ . '/app.php';

const DUMMY_HASH = '$2y$12$QOq9KkY3KXccVHKUXFnhaODFPslUMyV8eOShVG9LvP52Ft.ClSpZS';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/login') {
    if ($method !== 'POST') {
        respond(405, ['error' => 'Use POST.'], ['Allow' => 'POST']);
    }
    $email = postedString('email');
    $password = postedString('password');
    if ($email === '' || $password === '') {
        respond(422, ['error' => 'Email and password, please.']);
    }

    // The same answer, in the same time, whether the account exists or not.
    $id = userIdByEmail($email);
    $passwordOk = password_verify($password, $id === null ? DUMMY_HASH : USERS[$id]['hash']);
    if ($id === null || !$passwordOk) {
        respond(401, ['error' => 'Wrong email or password.'], ['WWW-Authenticate' => 'Bearer']);
    }
    respond(200, ['token' => array_search($id, TOKENS, true)]);
}

if ($path === '/notes') {
    if ($method !== 'POST') {
        respond(405, ['error' => 'Use POST.'], ['Allow' => 'POST']);
    }
    if (currentUserId() === null) {
        respond(401, ['error' => 'Log in first.'], ['WWW-Authenticate' => 'Bearer']);
    }
    $text = postedString('text');
    if ($text === '' || mb_strlen($text) > 500) {
        respond(422, ['error' => 'A note is 1 to 500 characters.']);
    }
    // A real app would save it here. This one pretends it got id 3.
    respond(201, ['id' => 3, 'text' => $text], ['Location' => '/notes/3']);
}

if (preg_match('#^/notes/(\d+)$#', $path, $m)) {
    if ($method !== 'GET') {
        respond(405, ['error' => 'Use GET.'], ['Allow' => 'GET']);
    }
    $userId = currentUserId();
    if ($userId === null) {
        respond(401, ['error' => 'Log in first.'], ['WWW-Authenticate' => 'Bearer']);
    }
    // Missing and not yours get the same 404, so a stranger can't tell which.
    $note = NOTES[(int) $m[1]] ?? null;
    if ($note === null || $note['owner'] !== $userId) {
        respond(404, ['error' => 'Note not found.']);
    }
    respond(200, ['text' => $note['text']]);
}

respond(404, ['error' => 'Not found.']);
