<?php
declare(strict_types=1);

// Never trust input: the name is whatever the visitor put in the URL.
$name = is_string($_GET['name'] ?? null) ? trim($_GET['name']) : '';
$sizes = ['Small', 'Medium', 'Large'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shirts</title>
</head>
<body>
    <header>
        <h1>Shirts</h1>
    </header>
    <main>
        <?php if ($name !== ''): ?>
            <p>Hello, <?= htmlspecialchars($name) ?>.</p>
        <?php endif; ?>

        <h2>Sizes</h2>
        <ul>
            <?php foreach ($sizes as $size): ?>
                <li><?= htmlspecialchars($size) ?></li>
            <?php endforeach; ?>
        </ul>

        <p><a href="/?name=Alice">Say hello to Alice</a></p>
    </main>
</body>
</html>
