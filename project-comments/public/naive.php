<?php
// BROKEN ON PURPOSE, for the Break It section. Never put this on a server.
// It takes every comment at once, from anyone, as fast as they can send them,
// and prints them back exactly as typed.
require dirname(__DIR__) . '/src/db.php';
$db = connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->prepare('INSERT INTO comments (post_id, name, body, ip, approved) VALUES (1, ?, ?, ?, 1)')
        ->execute([$_POST['name'], $_POST['body'], $_SERVER['REMOTE_ADDR']]);
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Comments (naive)</title>
<?php foreach ($db->query('SELECT name, body FROM comments WHERE approved = 1 ORDER BY id') as $c): ?>
    <p><strong><?= $c['name'] ?></strong> wrote: <?= $c['body'] ?></p>
<?php endforeach; ?>
<form method="post">
    <input name="name"> <textarea name="body"></textarea> <button type="submit">Post</button>
</form>
