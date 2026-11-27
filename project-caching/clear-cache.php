<?php
declare(strict_types=1);

// Throws away every cached value: run it after a deploy, or when the numbers
// behind the report change and can't wait for the minute to pass.
require __DIR__ . '/src/FileCache.php';

$removed = (new FileCache(__DIR__ . '/cache'))->clear();
echo "Removed $removed cached value(s).\n";
