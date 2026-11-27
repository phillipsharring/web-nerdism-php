<?php
declare(strict_types=1);

// The server's clock runs on UTC unless you say otherwise. Say so.
date_default_timezone_set('UTC');

$name = 'Alex Example';
$about = 'I build small websites that load fast and work without JavaScript.';
$links = [
    'Notes' => 'https://example.com/notes',
    'Code' => 'https://example.com/code',
    'The PHP manual' => 'https://www.php.net/manual/en/',
];

$hour = (int) date('G');
$greeting = match (true) {
    $hour < 12 => 'Good morning',
    $hour < 18 => 'Good afternoon',
    default => 'Good evening',
};
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($name) ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <main>
        <p><?= $greeting ?>. (It's <?= date('H:i') ?> UTC here.)</p>
        <h1><?= htmlspecialchars($name) ?></h1>
        <p><?= htmlspecialchars($about) ?></p>
        <ul>
            <?php foreach ($links as $label => $url): ?>
                <li><a href="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars($label) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </main>
    <footer>
        <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($name) ?>. Updated <?= date('F j, Y', filemtime(__FILE__)) ?>.</p>
    </footer>
</body>
</html>
