<?php
declare(strict_types=1);

// Notes live in data/notes.txt, next to this file, one per line.
$dir = __DIR__ . '/data';
$file = $dir . '/notes.txt';

$command = $argv[1] ?? 'list';

if ($command === 'add') {
    // Never trust input: it's text from outside, so check it before it goes in a file.
    // A new line inside it would start a second note, so it becomes a space.
    $note = trim(str_replace(["\r", "\n"], ' ', $argv[2] ?? ''));
    if ($note === '' || !mb_check_encoding($note, 'UTF-8') || mb_strlen($note) > 200) {
        echo "A note is 1 to 200 characters of text.\n";
        exit(1);
    }

    if (!is_dir($dir)) {
        mkdir($dir, 0700); // only the owner can read it
    }

    // FILE_APPEND adds to the end. LOCK_EX keeps two writers from mixing their lines.
    if (file_put_contents($file, $note . "\n", FILE_APPEND | LOCK_EX) === false) {
        echo "Couldn't write to $file\n";
        exit(1);
    }
    echo "Saved.\n";
    exit(0);
}

if ($command === 'list') {
    if (!is_file($file)) {
        echo "No notes yet.\n";
        exit(0);
    }
    // file() reads the whole file into an array of lines. Fine for small files.
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $index => $line) {
        echo $index + 1, '. ', $line, "\n";
    }
    exit(0);
}

echo "Use: php notes.php add \"a note\", or php notes.php list\n";
exit(1);
