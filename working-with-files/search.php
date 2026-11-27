<?php
declare(strict_types=1);

// Find the lines in a file that contain a word, reading one line at a time,
// so even a huge file never has to fit in memory.
$dataDir = realpath(__DIR__ . '/data');
$name = $argv[1] ?? '';
$word = $argv[2] ?? '';

// Never trust input: the name has to resolve to a file inside data/, and nowhere else.
// realpath() follows every ../ and link, so what's left is where the file really is.
$path = $dataDir === false ? false : realpath($dataDir . '/' . $name);
if ($path === false || !str_starts_with($path, $dataDir . '/') || !is_file($path)) {
    echo "Not a file in data/: $name\n";
    exit(1);
}

$handle = fopen($path, 'r');
$read = 0;
$found = 0;
while (($line = fgets($handle)) !== false) {
    $read++;
    if (str_contains($line, $word)) {
        $found++;
        echo rtrim($line, "\r\n"), "\n";
    }
}
fclose($handle);

echo "$found of $read lines matched\n";
