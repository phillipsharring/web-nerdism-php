<?php
declare(strict_types=1);

$name = 'Alex Example';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($name) ?></title>
</head>
<body>
    <h1><?= htmlspecialchars($name) ?></h1>
    <p>This page is made with PHP. Curious how?</p>
    <footer>
        <p><a href="/source.php?file=index.php">View the source of this page</a></p>
    </footer>
</body>
</html>
