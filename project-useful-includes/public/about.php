<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

$page = ['title' => 'About', 'path' => '/about.php', 'source' => 'about.php'];

require dirname(__DIR__) . '/templates/header.php';
?>
<h1>About</h1>
<p>Change the site name in <code>src/config.php</code>, and it changes here, on the home page, and in every title.</p>
<?php require dirname(__DIR__) . '/templates/footer.php'; ?>
