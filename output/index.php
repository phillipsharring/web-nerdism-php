<?php
// Never trust input: the name comes from the query string, so it's escaped on the way out.
$name = is_string($_GET['name'] ?? null) ? $_GET['name'] : 'world';
?>
<!doctype html>
<meta charset="utf-8">
<title>Output</title>
<p>Hello, <?= htmlspecialchars($name) ?>.</p>
