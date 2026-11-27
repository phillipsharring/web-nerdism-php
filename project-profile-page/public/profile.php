<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/session.php';
require dirname(__DIR__) . '/src/csrf.php';

start_session();
$userId = require_login();
$db = db();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // First: did this form come from our own page?
    csrf_check();

    // Never trust input.
    $field = function (string $name, int $max): ?string {
        $value = $_POST[$name] ?? '';
        if (!is_string($value) || !mb_check_encoding($value, 'UTF-8') || mb_strlen(trim($value)) > $max) {
            return null;
        }

        return trim($value);
    };
    $name = $field('display_name', 50);
    $bio = $field('bio', 500);

    if ($name === null || $name === '') {
        $errors[] = 'A display name, please, up to 50 characters.';
    }
    if ($bio === null) {
        $errors[] = 'A bio can be up to 500 characters.';
    }

    if (!$errors) {
        $db->prepare('UPDATE users SET display_name = ?, bio = ? WHERE id = ?')->execute([$name, $bio, $userId]);
        $_SESSION['flash'] = 'Saved.';
        redirect('/profile.php');
    }
}

$stmt = $db->prepare('SELECT display_name, bio FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$e = fn (string $text): string => htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<meta charset="utf-8">
<title>Profile</title>
<?php if ($flash): ?><p role="status"><?= $e($flash) ?></p><?php endif; ?>
<?php foreach ($errors as $error): ?><p><?= $e($error) ?></p><?php endforeach; ?>
<h1><?= $e($user['display_name']) ?></h1>
<p><?= nl2br($e($user['bio'])) ?></p>
<form method="post">
    <?= csrf_field() ?>
    <p><label>Display name <input name="display_name" value="<?= $e($user['display_name']) ?>" maxlength="50" required></label></p>
    <p><label>Bio <textarea name="bio" maxlength="500"><?= $e($user['bio']) ?></textarea></label></p>
    <p><button type="submit">Save</button></p>
</form>
