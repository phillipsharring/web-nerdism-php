<?php
declare(strict_types=1);

// The one way in: PHP's built-in server sends every request here (see the
// command in compose.yml), and this file decides what each one means.

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
if ($path === '/naive.php') {
    return false; // the Break It file: let the server run it as it is
}

require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/http.php';
require dirname(__DIR__) . '/src/tasks.php';

$method = $_SERVER['REQUEST_METHOD'];

// Which resource, and which methods it allows.
if ($path === '/api/tasks') {
    $id = null;
    $allowed = ['GET', 'POST'];
} elseif (preg_match('#^/api/tasks/([1-9][0-9]{0,9})$#', $path, $m)) {
    $id = (int) $m[1];
    $allowed = ['GET', 'PATCH', 'DELETE'];
} else {
    fail(404, 'No such resource.');
}
if (!in_array($method, $allowed, true)) {
    fail(405, "$method isn't allowed here.", ['Allow' => implode(', ', $allowed)]);
}

$db = connect();
$caller = authenticate($db);   // 401 unless the token is good
if ($method !== 'GET' && !$caller['can_write']) {
    fail(403, 'This token can read, but not change anything.');
}

// The list, and creating.
if ($id === null) {
    if ($method === 'GET') {
        $stmt = $db->prepare('SELECT id, title, done FROM tasks WHERE user_id = ? ORDER BY id');
        $stmt->execute([$caller['user_id']]);
        respond(200, ['tasks' => array_map('present', $stmt->fetchAll())]);
    }
    [$fields, $errors] = validateTask(readJson(), partial: false);
    if ($errors) {
        respond(422, ['error' => 'Some fields need fixing.', 'fields' => $errors]);
    }
    $db->prepare('INSERT INTO tasks (user_id, title) VALUES (?, ?)')->execute([$caller['user_id'], $fields['title']]);
    $new = (int) $db->lastInsertId();
    respond(201, present(['id' => $new, 'title' => $fields['title'], 'done' => 0]), ['Location' => "/api/tasks/$new"]);
}

// One task. Only the caller's own: someone else's task is a 404, the same as
// one that doesn't exist, so the API never confirms what other people have.
$stmt = $db->prepare('SELECT id, title, done FROM tasks WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $caller['user_id']]);
$task = $stmt->fetch() ?: fail(404, 'No such task.');

if ($method === 'GET') {
    respond(200, present($task));
}
if ($method === 'DELETE') {
    $db->prepare('DELETE FROM tasks WHERE id = ?')->execute([$id]);
    respond(204);
}

// PATCH: change only the fields that were sent.
[$fields, $errors] = validateTask(readJson(), partial: true);
if ($errors) {
    respond(422, ['error' => 'Some fields need fixing.', 'fields' => $errors]);
}
if ($fields) {
    $set = implode(', ', array_map(fn ($f) => "$f = ?", array_keys($fields))); // keys from our own list
    $db->prepare("UPDATE tasks SET $set WHERE id = ?")->execute([...array_values($fields), $id]);
}
respond(200, present(array_merge($task, $fields)));
