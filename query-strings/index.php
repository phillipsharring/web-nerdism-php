<?php
header('Content-Type: text/plain');

// Never trust input: each one can be missing, the wrong type, or nonsense.
$q = is_string($_GET['q'] ?? null) ? $_GET['q'] : '';
$page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
$tags = array_filter((array) ($_GET['tag'] ?? []), 'is_string');

echo "Searching for: $q\n";
echo "Page: $page\n";
echo 'Tags: ', implode(', ', $tags), "\n\n";
echo "The contents of \$_GET:\n";
print_r($_GET);
