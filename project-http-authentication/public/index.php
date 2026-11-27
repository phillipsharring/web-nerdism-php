<?php
declare(strict_types=1);

require dirname(__DIR__) . '/auth.php';

$users = require dirname(__DIR__) . '/users.php';
$user = requireBasicAuth($users, 'Staff only');

// A page about one person: no cache in the middle should keep a copy.
header('Cache-Control: private, no-store');
?>
<!doctype html>
<meta charset="utf-8">
<title>Staff only</title>
<h1>Staff only</h1>
<p>Hello, <?= htmlspecialchars($user) ?>. You're in.</p>
