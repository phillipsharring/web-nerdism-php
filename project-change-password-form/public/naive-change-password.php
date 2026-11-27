<?php
// The naive version: a signed-in session and a new password are all it
// takes. No token, no current password. Don't copy this one.
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/session.php';

start_session();
$db = db();
$user = require_login($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
        ->execute([password_hash((string) ($_POST['new_password'] ?? ''), PASSWORD_DEFAULT), $user['id']]);
    $_SESSION['flash'] = 'Password changed.';
    redirect('/account.php');
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Change password (naive)</title>
<form method="post">
    <p><label>New password <input type="password" name="new_password" autocomplete="new-password" required></label></p>
    <p><button type="submit">Change password</button></p>
</form>
