<?php
declare(strict_types=1);

// The only files anyone may look at. The key is what goes in the URL; the
// value is the real path. Nothing from the URL ever becomes part of a path.
$allowed = [
    'index.php' => __DIR__ . '/index.php',
    'source.php' => __FILE__,
];

// Never trust input: the file name comes from the query string.
$file = $_GET['file'] ?? '';
if (!is_string($file) || !array_key_exists($file, $allowed)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo "No source for that.\n";
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Source of <?= htmlspecialchars($file) ?></title>
</head>
<body>
    <h1>Source of <?= htmlspecialchars($file) ?></h1>
    <?php highlight_file($allowed[$file]); ?>
    <p><a href="/">Back</a> &middot; <a href="/source.php?file=source.php">The source of this page</a></p>
</body>
</html>
