<?php
declare(strict_types=1);

// Never trust input. The theme goes into the page as a class name, so it has
// to be one of ours, exactly.
$themes = ['light', 'dark'];
$theme = $_GET['theme'] ?? 'light';
if (!in_array($theme, $themes, true)) {
    $theme = 'light';
}

// When the stylesheet last changed, in its URL: change the file, and the URL
// changes with it, so no browser keeps using an old copy.
$version = filemtime(__DIR__ . '/style.css');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shirts</title>
    <link rel="stylesheet" href="/style.css?v=<?= $version ?>">
</head>
<body class="<?= $theme ?>">
    <h1>Shirts</h1>
    <p class="note">Three sizes, two themes.</p>
    <p><a href="/?theme=light">Light</a> <a href="/?theme=dark">Dark</a></p>
</body>
</html>
