<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/session.php';

start_session();
header('Cache-Control: private, no-store');

// The naive sign-in stores 'user'; the fixed one, 'email'.
$email = $_SESSION['email'] ?? $_SESSION['user'] ?? null;
?>
<!doctype html>
<meta charset="utf-8">
<title>Account</title>
<?php if ($email === null): ?>
    <p>Not signed in. <a href="/login.php">Sign in</a></p>
<?php else: ?>
    <p>Signed in as <?= htmlspecialchars($email) ?>.</p>
<?php endif; ?>
