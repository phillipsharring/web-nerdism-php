<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/auth.php';

start_session();
$users = require dirname(__DIR__) . '/src/users.php';

// Never trust input: where to go next must be a path on this site.
$next = local_path($_POST['next'] ?? $_GET['next'] ?? null, '/members.php');
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $hash = $users[strtolower($email)] ?? null;

    if ($hash !== null && password_verify($password, $hash)) {
        sign_in(strtolower($email));
        redirect($next);
    }
    // The same answer either way: don't tell anyone which addresses are members.
    $error = 'Wrong email or password.';
}

$e = fn (string $text): string => htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<meta charset="utf-8">
<title>Sign in</title>
<h1>Sign In</h1>
<?php if ($error): ?><p><?= $e($error) ?></p><?php endif; ?>
<form method="post">
    <input type="hidden" name="next" value="<?= $e($next) ?>">
    <p><label>Email <input type="email" name="email" value="<?= $e($email) ?>" autocomplete="username" required></label></p>
    <p><label>Password <input type="password" name="password" autocomplete="current-password" required></label></p>
    <p><button type="submit">Sign in</button></p>
</form>
