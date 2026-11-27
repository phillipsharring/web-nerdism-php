<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

// The View Source Link project's viewer, with the shared pieces on its list.
$root = dirname(__DIR__);
$allowed = [
    'index.php' => __DIR__ . '/index.php',
    'about.php' => __DIR__ . '/about.php',
    'source.php' => __FILE__,
    'bootstrap.php' => $root . '/src/bootstrap.php',
    'functions.php' => $root . '/src/functions.php',
    'header.php' => $root . '/templates/header.php',
    'footer.php' => $root . '/templates/footer.php',
];

// Never trust input: the file name comes from the query string.
$file = $_GET['file'] ?? '';
if (!is_string($file) || !array_key_exists($file, $allowed)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo "No source for that.\n";
    exit;
}

$page = ['title' => "Source of $file", 'path' => '/source.php', 'source' => 'source.php'];

require $root . '/templates/header.php';
?>
<h1>Source of <?= e($file) ?></h1>
<?php highlight_file($allowed[$file]); ?>
<p>Also:
    <?php foreach (array_keys($allowed) as $name): ?>
        <a href="/source.php?file=<?= e($name) ?>"><?= e($name) ?></a>
    <?php endforeach; ?>
</p>
<?php require $root . '/templates/footer.php'; ?>
