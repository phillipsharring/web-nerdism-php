<?php
// The naive version: it keeps the file under the name it came with, in a
// folder the web server hands out. Don't copy this one.
declare(strict_types=1);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $name = $_FILES['photo']['name'];
    move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/uploads/' . $name);
    $message = "Saved. It's at /uploads/$name";
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Upload a photo (naive)</title>

<p><?= htmlspecialchars($message) ?></p>

<form method="post" enctype="multipart/form-data">
    <p><label>Photo <input type="file" name="photo" accept="image/*" required></label></p>
    <p><button type="submit">Upload</button></p>
</form>
