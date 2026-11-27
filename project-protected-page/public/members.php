<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/auth.php';

start_session();
$user = require_login();
?>
<!doctype html>
<meta charset="utf-8">
<title>Members</title>
<h1>Members Only</h1>
<p>Hello, <?= htmlspecialchars($user) ?>. The meeting is Thursday at 7.</p>
<form method="post" action="/logout.php">
    <button type="submit">Sign out</button>
</form>
