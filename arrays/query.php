<?php
declare(strict_types=1);

// Never trust input: a query string can turn any value into an array.
header('Content-Type: text/plain');
var_dump($_GET);

$tag = $_GET['tag'] ?? '';
echo is_string($tag) ? "One tag: $tag\n" : "Refused: tag should be one value, not a list\n";
