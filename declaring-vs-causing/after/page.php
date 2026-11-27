<?php

declare(strict_types=1);

// Causes. The settings, the log and the output happen here, where you can see them.
require __DIR__ . '/text.php';

date_default_timezone_set('America/New_York');
ini_set('display_errors', '1');
file_put_contents(__DIR__ . '/visits.log', date('c') . "\n", FILE_APPEND);

echo "<nav><a href=\"/\">Home</a></nav>\n";
echo '<p>Read <a href="/posts/', slugify('Hello, World'), '">Hello, World</a></p>', "\n";
