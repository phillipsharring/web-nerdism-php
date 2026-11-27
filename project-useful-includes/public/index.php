<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

$page = ['title' => 'Home', 'path' => '/', 'source' => 'index.php'];

require dirname(__DIR__) . '/templates/header.php';
?>
<h1>Hello</h1>
<p>Every page on this site shares one header, one footer and one set of settings.</p>
<?php require dirname(__DIR__) . '/templates/footer.php'; ?>
