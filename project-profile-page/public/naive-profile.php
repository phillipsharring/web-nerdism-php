<?php
// The naive profile: anything posted here, with Alice's cookie, changes
// Alice's profile, wherever the form came from. Don't copy this one.
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/session.php';

start_session();
$userId = require_login();
$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->prepare('UPDATE users SET display_name = ?, bio = ? WHERE id = ?')
        ->execute([(string) ($_POST['display_name'] ?? ''), (string) ($_POST['bio'] ?? ''), $userId]);
    redirect('/naive-profile.php');
}

$stmt = $db->prepare('SELECT display_name, bio FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>
<!doctype html>
<meta charset="utf-8">
<title>Profile (naive)</title>
<h1><?= htmlspecialchars($user['display_name']) ?></h1>
<p><?= htmlspecialchars($user['bio']) ?></p>
<form method="post">
    <p><label>Display name <input name="display_name" value="<?= htmlspecialchars($user['display_name']) ?>"></label></p>
    <p><label>Bio <textarea name="bio"><?= htmlspecialchars($user['bio']) ?></textarea></label></p>
    <p><button type="submit">Save</button></p>
</form>
