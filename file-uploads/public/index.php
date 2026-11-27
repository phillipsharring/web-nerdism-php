<?php
// The fixed version. Never trust input: not the file, not its name, not the
// type the browser claims. Check what it really is, name it yourself, and keep
// it where the web server won't run it.
declare(strict_types=1);

require __DIR__ . '/uploads.php';

function saveUpload(mixed $file): string
{
    if (!is_array($file) || !is_string($file['tmp_name'] ?? null) || ($file['error'] ?? null) !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('Choose one photo to upload.');
    }
    if ($file['size'] > MAX_BYTES) {
        throw new InvalidArgumentException('That file is over 2 MB.');
    }
    // What the bytes say it is. $file['type'] is what the browser said, so it's ignored.
    $type = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset(TYPES[$type]) || getimagesize($file['tmp_name']) === false) {
        throw new InvalidArgumentException('That is not a PNG, JPEG or GIF.');
    }
    // A name you made, with an extension you chose.
    $id = bin2hex(random_bytes(16)) . '.' . TYPES[$type];
    if (!move_uploaded_file($file['tmp_name'], STORAGE . '/' . $id)) {
        throw new RuntimeException('The upload could not be saved.');
    }
    return $id;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = saveUpload($_FILES['photo'] ?? null);
        $message = "Saved. It's at /image.php?id=$id";
    } catch (InvalidArgumentException $e) {
        http_response_code(422);
        $message = $e->getMessage();
    }
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Upload a photo</title>

<p><?= htmlspecialchars($message) ?></p>

<form method="post" enctype="multipart/form-data">
    <p><label>Photo <input type="file" name="photo" accept="image/png,image/jpeg,image/gif" required></label></p>
    <p><button type="submit">Upload</button></p>
</form>
