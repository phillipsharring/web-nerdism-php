<!doctype html>
<meta charset="utf-8">
<title>PHP Files</title>

<h1>Hello from a PHP file</h1>
<p>This line is plain HTML. PHP copies it out untouched.</p>

<?php
// Between the tags is PHP. It runs, and only what it prints is sent.
$name = 'world';
$hour = (int) date('G');
?>
<p>Hello, <?= $name ?>.</p>
<p>The server's clock says <?= date('H:i') ?>.</p>

<?php if ($hour < 12): ?>
    <p>Good morning.</p>
<?php else: ?>
    <p>Good afternoon.</p>
<?php endif; ?>
