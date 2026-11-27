<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/session.php';
require dirname(__DIR__) . '/src/csrf.php';
require dirname(__DIR__) . '/src/LoginLimiter.php';

const MIN_LENGTH = 12;
const MAX_BYTES = 72;

start_session();
$db = db();
$user = require_login($db);
$limiter = new LoginLimiter($db);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. From our own form?
    csrf_check();

    // Never trust input.
    $text = fn (string $name): string => is_string($_POST[$name] ?? null) ? $_POST[$name] : '';
    $current = $text('current_password');
    $new = $text('new_password');
    $confirm = $text('confirm_password');
    $ip = $_SERVER['REMOTE_ADDR'];

    // 2. Is it really them? A wrong current password counts as a failed
    //    sign-in, so this form can't be used to guess it either.
    if ($limiter->tooMany($user['email'], $ip)) {
        http_response_code(429);
        $errors[] = 'Too many attempts. Please wait 15 minutes and try again.';
    } elseif (!password_verify($current, $user['password_hash'])) {
        $limiter->fail($user['email'], $ip);
        $errors[] = 'Your current password is wrong.';
    // 3. Is the new one any good?
    } elseif (!mb_check_encoding($new, 'UTF-8') || mb_strlen($new) < MIN_LENGTH) {
        $errors[] = 'A new password of at least ' . MIN_LENGTH . ' characters, please.';
    } elseif (strlen($new) > MAX_BYTES) {
        $errors[] = 'That password is too long. Up to ' . MAX_BYTES . ' letters, fewer with accents or emoji.';
    } elseif ($new !== $confirm) {
        $errors[] = "The new passwords don't match.";
    } elseif (password_verify($new, $user['password_hash'])) {
        $errors[] = "That's the password you have now.";
    } else {
        // 4. Change it, and sign out every other session.
        $db->prepare('UPDATE users SET password_hash = ?, session_version = session_version + 1 WHERE id = ?')
            ->execute([password_hash($new, PASSWORD_DEFAULT), $user['id']]);

        // This session stays signed in, with a new id, a new version and a new token.
        session_regenerate_id(true);
        $_SESSION['session_version'] = (int) $user['session_version'] + 1;
        unset($_SESSION['csrf']);
        $_SESSION['flash'] = 'Password changed. Any other devices have been signed out.';
        redirect('/account.php');
    }
}

$e = fn (string $text): string => htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<meta charset="utf-8">
<title>Change password</title>
<?php foreach ($errors as $error): ?><p><?= $e($error) ?></p><?php endforeach; ?>
<form method="post">
    <?= csrf_field() ?>
    <!-- Lets password managers know whose password this is. -->
    <input type="hidden" name="username" value="<?= $e($user['email']) ?>" autocomplete="username">
    <p><label>Current password <input type="password" name="current_password" autocomplete="current-password" required></label></p>
    <p><label>New password <input type="password" name="new_password" minlength="<?= MIN_LENGTH ?>" autocomplete="new-password" required></label></p>
    <p><label>New password, again <input type="password" name="confirm_password" minlength="<?= MIN_LENGTH ?>" autocomplete="new-password" required></label></p>
    <p><button type="submit">Change password</button></p>
</form>
