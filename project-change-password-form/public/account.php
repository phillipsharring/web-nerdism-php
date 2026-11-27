<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/session.php';

start_session();
$user = require_login(db());
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<meta charset="utf-8">
<title>Account</title>
<?php if ($flash): ?><p role="status"><?= htmlspecialchars($flash) ?></p><?php endif; ?>
<p>Signed in as <?= htmlspecialchars($user['email']) ?>.</p>
<p><a href="/change-password.php">Change your password</a></p>
