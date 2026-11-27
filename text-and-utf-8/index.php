<?php
declare(strict_types=1);

// Never trust input. A name has to be a string, valid UTF-8, and not too long.
$name = $_GET['name'] ?? '';
$error = '';
if (!is_string($name) || !mb_check_encoding($name, 'UTF-8')) {
    $error = 'That name is not valid UTF-8 text.';
    $name = '';
} elseif (mb_strlen($name) > 20) {
    $error = 'Twenty characters at most, please.';
}

// Say the charset in the header and in the page, so the browser reads the bytes as UTF-8.
header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html>
<html lang="en">
<meta charset="utf-8">
<title>Text and UTF-8</title>

<?php if ($error !== ''): ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php elseif ($name !== ''): ?>
    <p>Hello, <?= htmlspecialchars($name) ?>: <?= mb_strlen($name) ?> characters, <?= strlen($name) ?> bytes.</p>
<?php endif; ?>

<form>
    <label>Your name <input name="name" value="<?= htmlspecialchars($name) ?>"></label>
    <button type="submit">Count</button>
</form>
