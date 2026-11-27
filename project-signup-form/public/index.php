<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';

const MIN_LENGTH = 12;
const MAX_BYTES = 72; // bcrypt ignores everything after the 72nd byte

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Never trust input. Check the type before anything else.
    $email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($email) > 254) {
        $errors[] = 'An email address, please.';
    }

    // Passwords are not trimmed: a space is a character like any other.
    $common = file(dirname(__DIR__) . '/src/common-passwords.txt', FILE_IGNORE_NEW_LINES);
    if (!mb_check_encoding($password, 'UTF-8') || mb_strlen($password) < MIN_LENGTH) {
        $errors[] = 'A password of at least ' . MIN_LENGTH . ' characters, please. A few words in a row works well.';
    } elseif (strlen($password) > MAX_BYTES) {
        $errors[] = 'That password is too long. Up to ' . MAX_BYTES . ' letters, fewer with accents or emoji.';
    } elseif (in_array(mb_strtolower($password), $common, true)) {
        $errors[] = "That's one of the most common passwords there is. Pick another.";
    }

    if (!$errors) {
        try {
            $stmt = db()->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
            $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);
            header('Location: /?created=1', true, 303);
            exit;
        } catch (PDOException $e) {
            // 1062 is MySQL's "duplicate entry": the UNIQUE index said no.
            if (($e->errorInfo[1] ?? null) !== 1062) {
                throw $e;
            }
            $errors[] = 'That address already has an account. Try signing in.';
        }
    }
}

$e = fn (string $text): string => htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<meta charset="utf-8">
<title>Sign up</title>
<?php if (isset($_GET['created'])): ?>
    <p>Account created. Welcome.</p>
<?php endif; ?>
<?php foreach ($errors as $error): ?>
    <p><?= $e($error) ?></p>
<?php endforeach; ?>
<form method="post">
    <p><label>Email <input type="email" name="email" value="<?= $e($email) ?>" autocomplete="email" required></label></p>
    <p><label>Password <input type="password" name="password" minlength="<?= MIN_LENGTH ?>" autocomplete="new-password" required></label></p>
    <p><button type="submit">Sign up</button></p>
</form>
