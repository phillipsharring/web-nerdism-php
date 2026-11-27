<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/services.php';

// Opening the link shows a button; only pressing it confirms. Mail scanners
// open links to check them, and a scanner isn't a person saying yes.
$token = $_POST['token'] ?? $_GET['token'] ?? '';
$token = is_string($token) && preg_match('/^[a-f0-9]{64}$/', $token) ? $token : '';
$done = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $done = $token !== '' && $subscriptions->confirm($token);
    http_response_code($done ? 200 : 400);
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Confirm</title>
<?php if ($done === true): ?>
    <p>You're subscribed. The next issue comes to you.</p>
<?php elseif ($done === false): ?>
    <p>That link has expired or was already used. Sign up again to get a fresh one.</p>
<?php else: ?>
    <form method="post">
        <input type="hidden" name="token" value="<?= $token ?>">
        <p><button type="submit">Yes, send me the newsletter</button></p>
    </form>
<?php endif; ?>
