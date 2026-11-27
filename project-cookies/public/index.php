<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/cookies.php';

const THIRTY_DAYS = 60 * 60 * 24 * 30;
const MAX_NAME = 50;

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['forget'])) {
        forget_cookie('name');
        header('Location: /', true, 303);
        exit;
    }

    // Never trust input: check it before it goes in a cookie.
    $name = is_string($_POST['name'] ?? null) ? trim($_POST['name']) : '';
    if ($name === '' || mb_strlen($name) > MAX_NAME || !mb_check_encoding($name, 'UTF-8')) {
        $error = 'A name, please, up to ' . MAX_NAME . ' characters.';
    } else {
        set_cookie('name', $name, THIRTY_DAYS);
        header('Location: /', true, 303);
        exit;
    }
}

$name = cookie_text('name', MAX_NAME);
?>
<!doctype html>
<meta charset="utf-8">
<title>Remember me</title>
<?php if ($name !== ''): ?>
    <p>Welcome back, <?= htmlspecialchars($name) ?>.</p>
    <form method="post"><button type="submit" name="forget">Forget me</button></form>
<?php else: ?>
    <?php if ($error): ?><p><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post">
        <label>Your name <input name="name" maxlength="<?= MAX_NAME ?>" required></label>
        <button type="submit">Remember me</button>
    </form>
<?php endif; ?>
