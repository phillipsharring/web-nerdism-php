<?php
// The fixed version: every form carries a secret token from the session,
// and a POST without the right one is refused.
declare(strict_types=1);

require __DIR__ . '/account.php';

// One random token per session. Another site can make the browser send the
// cookie, but it can't read this page, so it can't know the token.
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? null;
    if (!is_string($token) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(403);
        exit("That form didn't come from this site.\n");
    }
    $email = newEmail();
    if ($email === null) {
        http_response_code(422);
        $message = 'That is not an email address.';
    } else {
        $_SESSION['email'] = $email;
        $message = 'Changed.';
    }
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Your account</title>

<p>Your email address: <?= e($_SESSION['email']) ?></p>
<?php if ($message !== ''): ?>
    <p><?= e($message) ?></p>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
    <p><label>New email <input type="email" name="email" required></label></p>
    <p><button type="submit">Change it</button></p>
</form>
