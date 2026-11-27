<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/session.php';
require dirname(__DIR__) . '/src/LoginLimiter.php';

start_session();

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = db();
    $limiter = new LoginLimiter($db);

    // Never trust input.
    $email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    // Behind a proxy, use the address it reports. See What's in the Middle.
    $ip = $_SERVER['REMOTE_ADDR'];

    if ($limiter->tooMany($email, $ip)) {
        http_response_code(429);
        header('Retry-After: 900');
        $error = 'Too many attempts. Please wait 15 minutes and try again.';
    } else {
        $stmt = $db->prepare('SELECT id, email, password_hash FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Check a password even when there's no such user, so a wrong address
        // takes as long as a wrong password and the timing gives nothing away.
        $hash = $user['password_hash'] ?? '$2y$12$V7YmYyHThlzBFxJ50/IaK.NxdbvumRLq0B6SP9OoE1Zzm8.NxxwmS';
        $ok = password_verify($password, $hash) && $user !== false;

        if ($ok) {
            // A brand new id the moment someone signs in. The old one might
            // be known to someone else.
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];

            if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                $db->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                    ->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
            }
            redirect('/account.php');
        }

        $limiter->fail($email, $ip);
        $error = 'Wrong email or password.';
    }
}

$e = fn (string $text): string => htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<meta charset="utf-8">
<title>Sign in</title>
<?php if ($error): ?><p><?= $e($error) ?></p><?php endif; ?>
<form method="post">
    <p><label>Email <input type="email" name="email" value="<?= $e($email) ?>" autocomplete="username" required></label></p>
    <p><label>Password <input type="password" name="password" autocomplete="current-password" required></label></p>
    <p><button type="submit">Sign in</button></p>
</form>
