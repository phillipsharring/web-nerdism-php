<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/auth.php';

start_session();
$user = current_user();
?>
<!doctype html>
<meta charset="utf-8">
<title>Home</title>
<h1>Home</h1>
<p>Anyone can see this page.</p>
<p><a href="/members.php">The members' page</a></p>
<?php if ($user !== null): ?>
    <p>Signed in as <?= htmlspecialchars($user) ?>.</p>
<?php endif; ?>
