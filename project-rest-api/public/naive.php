<?php
// BROKEN ON PURPOSE, for the Break It section. Never put this on a server.
// It believes whoever the caller says they are, and answers 200 to everything.
require dirname(__DIR__) . '/src/db.php';

header('Content-Type: application/json');
$db = connect();
if (isset($_GET['task'])) {
    $stmt = $db->prepare('SELECT id, title, done FROM tasks WHERE id = ?');
    $stmt->execute([$_GET['task']]);
    echo json_encode($stmt->fetch() ?: ['error' => 'not found']), "\n";
    exit;
}
$stmt = $db->prepare('SELECT id, title, done FROM tasks WHERE user_id = ?');
$stmt->execute([$_GET['user_id'] ?? 1]);
echo json_encode(['tasks' => $stmt->fetchAll()]), "\n";
