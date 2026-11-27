<?php
$sizes = ['small', 'medium', 'large'];
$posted = $_SERVER['REQUEST_METHOD'] === 'POST';

// Never trust input. The browser's checks are a courtesy; these are the real ones.
$name = is_string($_POST['name'] ?? null) ? trim($_POST['name']) : '';
$size = $_POST['size'] ?? '';
$wrap = isset($_POST['wrap']);

$errors = [];
if ($posted && $name === '') {
    $errors[] = 'Your name, please.';
}
if ($posted && !in_array($size, $sizes, true)) {
    $errors[] = 'Pick a size from the list.';
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Order a shirt</title>

<?php if ($posted && !$errors): ?>
    <p>Thanks, <?= htmlspecialchars($name) ?>: one <?= $size ?> shirt<?= $wrap ? ', gift wrapped' : '' ?>.</p>
<?php endif; ?>
<?php foreach ($errors as $error): ?>
    <p><?= $error ?></p>
<?php endforeach; ?>

<form method="post">
    <p><label>Name <input name="name" value="<?= htmlspecialchars($name) ?>" required></label></p>
    <p><label>Size
        <select name="size" required>
            <option value="">Pick one</option>
            <?php foreach ($sizes as $s): ?>
                <option<?= $s === $size ? ' selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
    </label></p>
    <p><label><input type="checkbox" name="wrap"<?= $wrap ? ' checked' : '' ?>> Gift wrap</label></p>
    <p><button type="submit">Order</button></p>
</form>
