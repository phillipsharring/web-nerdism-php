<?php
declare(strict_types=1);

// Stands in for slow work: a big query, a call to another site. It takes two
// seconds every time, and it stamps the time, so you can tell a copy.
function buildPage(): string
{
    sleep(2);
    $builtAt = gmdate('H:i:s');

    return <<<HTML
        <!doctype html>
        <html lang="en">
        <meta charset="utf-8">
        <title>Store Hours</title>
        <h1>Store Hours</h1>
        <p>Monday to Friday, 9 to 5.</p>
        <p>Built at $builtAt UTC.</p>
        </html>

        HTML;
}

// Our own cache: one file, outside the web root, good for 60 seconds.
$cacheFile = dirname(__DIR__) . '/cache/store-hours.html';
$isFresh = is_file($cacheFile) && filemtime($cacheFile) > time() - 60;

if ($isFresh) {
    $page = file_get_contents($cacheFile);
    header('X-Cache: HIT');
} else {
    $page = buildPage();
    // Write a temporary file, then rename it into place, so a request that
    // reads at the same moment never gets half a file.
    $temporaryFile = $cacheFile . '.' . bin2hex(random_bytes(4));
    file_put_contents($temporaryFile, $page);
    rename($temporaryFile, $cacheFile);
    header('X-Cache: MISS');
}

// The browser's cache, and any in the middle: the same page for everyone, so
// public, and good for 60 seconds. The ETag is a fingerprint of this version.
$etag = '"' . hash('sha256', $page) . '"';
header('Cache-Control: public, max-age=60');
header("ETag: $etag");

// If-None-Match is a header, so it's input: we only ever compare it to our
// own fingerprint, never use it for anything else.
if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
    http_response_code(304);
    exit;
}

header('Content-Type: text/html; charset=utf-8');
echo $page;
