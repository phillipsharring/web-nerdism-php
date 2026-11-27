<?php

declare(strict_types=1);

// A REST API for notes. The URL names the thing; the method says what to do with it.
require dirname(__DIR__) . '/src/http.php';
require dirname(__DIR__) . '/src/NoteStore.php';

$store = new NoteStore(sys_get_temp_dir() . '/rest-notes.json');
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// The collection: /notes
if ($path === '/notes') {
    $notes = $store->all();
    if ($method === 'GET') {
        $list = [];
        foreach ($notes as $id => $body) {
            $list[] = ['id' => $id, 'body' => $body];
        }
        send(200, ['notes' => $list]);
    }
    if ($method === 'POST') {
        $id = $notes === [] ? 1 : max(array_keys($notes)) + 1;
        $notes[$id] = noteFromRequest();
        $store->save($notes);
        send(201, ['id' => $id, 'body' => $notes[$id]], ['Location' => "/notes/$id"]);
    }
    send(405, ['error' => 'Use GET or POST.'], ['Allow' => 'GET, POST']);
}

// One note: /notes/{id}
if (preg_match('#^/notes/(\d+)$#', $path, $m)) {
    $id = (int) $m[1];
    $notes = $store->all();
    if (!in_array($method, ['GET', 'PUT', 'DELETE'], true)) {
        send(405, ['error' => 'Use GET, PUT or DELETE.'], ['Allow' => 'GET, PUT, DELETE']);
    }
    if (!isset($notes[$id])) {
        send(404, ['error' => 'No such note.']);
    }
    if ($method === 'GET') {
        send(200, ['id' => $id, 'body' => $notes[$id]]);
    }
    if ($method === 'PUT') {
        $notes[$id] = noteFromRequest();
        $store->save($notes);
        send(200, ['id' => $id, 'body' => $notes[$id]]);
    }
    unset($notes[$id]);
    $store->save($notes);
    send(204);
}

send(404, ['error' => 'Not found.']);
