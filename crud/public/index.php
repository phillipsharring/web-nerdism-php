<?php

declare(strict_types=1);

// One front door, five routes: Create, Read, Update, Delete, and the edit form.
require dirname(__DIR__) . '/src/notes.php';
require dirname(__DIR__) . '/src/views.php';
$db = require dirname(__DIR__) . '/src/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$id = preg_match('#^/notes/(\d+)(/edit|/delete)?$#', $path, $m) ? (int) $m[1] : null;
$action = $m[2] ?? '';

// Read: the list.
if ($method === 'GET' && $path === '/') {
    listPage(allNotes($db));
    exit;
}

// Create.
if ($method === 'POST' && $path === '/notes') {
    $error = noteBodyError($_POST['body'] ?? null);
    if ($error !== null) {
        http_response_code(422);
        listPage(allNotes($db), $error);
        exit;
    }
    createNote($db, trim($_POST['body']));
    header('Location: /', true, 303);
    exit;
}

// Everything else is about one note, which has to exist.
$note = $id === null ? null : findNote($db, $id);
if ($note === null) {
    http_response_code(404);
    page('Not found', "<h1>No such note.</h1>\n");
    exit;
}

// Read: one note, in a form to change it.
if ($method === 'GET' && $action === '/edit') {
    editPage($note);
    exit;
}

// Update.
if ($method === 'POST' && $action === '') {
    $error = noteBodyError($_POST['body'] ?? null);
    if ($error !== null) {
        http_response_code(422);
        editPage($note, $error);
        exit;
    }
    updateNote($db, $id, trim($_POST['body']));
    header('Location: /', true, 303);
    exit;
}

// Delete.
if ($method === 'POST' && $action === '/delete') {
    deleteNote($db, $id);
    header('Location: /', true, 303);
    exit;
}

http_response_code(405);
page('Not allowed', "<h1>Not allowed.</h1>\n");
