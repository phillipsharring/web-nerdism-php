<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page['title']) ?> | <?= e($config['site_name']) ?></title>
</head>
<body>
    <header>
        <p><?= e($config['site_name']) ?></p>
        <nav>
            <?php foreach ($config['nav'] as $href => $label): ?>
                <?= nav_link($href, $label, $page['path']) ?>
            <?php endforeach; ?>
        </nav>
    </header>
    <main>
