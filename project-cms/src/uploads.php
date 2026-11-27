<?php
declare(strict_types=1);

const MAX_IMAGE_BYTES = 2 * 1024 * 1024;

// The types we accept, by what the file actually is, and the extension each
// one gets. Not by the name it came with, and not by the type the browser sent.
const IMAGE_TYPES = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp'];

/**
 * Checks an uploaded image and moves it into $dir under a name we choose.
 * Returns that name. Throws with a message for the person if it's no good.
 *
 * Never trust input. Everything in $_FILES came from the client: the name,
 * the type, even the size can't be taken at its word. Only the bytes on disk
 * count, and those are input too.
 */
function storeImage(array $file, string $dir): string
{
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
        throw new RuntimeException('That image is too big: 2 MB at most.');
    }
    if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'] ?? '')) {
        throw new RuntimeException('The upload didn\'t arrive. Try again.');
    }
    if (filesize($file['tmp_name']) > MAX_IMAGE_BYTES) {
        throw new RuntimeException('That image is too big: 2 MB at most.');
    }

    // What is it really? Look at the bytes, then make sure it parses as an image.
    $type = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $size = @getimagesize($file['tmp_name']);
    if (!isset(IMAGE_TYPES[$type]) || $size === false || $size['mime'] !== $type) {
        throw new RuntimeException('PNG, JPEG or WebP images only.');
    }

    // Our name, our extension: random, so nobody can guess it, choose it, or
    // overwrite someone else's file with it.
    $name = bin2hex(random_bytes(16)) . '.' . IMAGE_TYPES[$type];
    if (!move_uploaded_file($file['tmp_name'], "$dir/$name")) {
        throw new RuntimeException('The image couldn\'t be saved.');
    }

    return $name;
}
