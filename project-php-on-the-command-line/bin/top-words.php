<?php
declare(strict_types=1);

// This is a command-line tool. If it ever ends up in a web root, refuse.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/src/words.php';

const USAGE = <<<TXT
    Usage: top-words.php [--top=N] [--min-length=N] [FILE...]

    Prints the most common words in the files, or in what's piped in if there
    are none.

      --top=N         how many to show (default 10)
      --min-length=N  skip words shorter than this (default 1)
      -h, --help      show this

    TXT;

/** Says what went wrong on STDERR, where errors go, and stops with $code. */
function quit(string $message, int $code): never
{
    fwrite(STDERR, $message . "\n");
    exit($code);
}

// Never trust input. Arguments are typed by a person, or by a script
// written by one: check every one.
$options = getopt('h', ['top:', 'min-length:', 'help'], $rest);
$files = array_slice($argv, $rest);

if (isset($options['h']) || isset($options['help'])) {
    echo USAGE;
    exit(0);
}
foreach ($files as $file) {
    if (str_starts_with($file, '-')) {
        quit("Unknown option: $file\n\n" . USAGE, 2);
    }
}

$top = filter_var($options['top'] ?? 10, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$minLength = filter_var($options['min-length'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($top === false || $minLength === false) {
    quit("--top and --min-length take a whole number, 1 or more.\n\n" . USAGE, 2);
}

// No files: read what's piped in. A person at a keyboard gets a hint first.
if ($files === []) {
    if (stream_isatty(STDIN)) {
        fwrite(STDERR, "Reading from the keyboard; press Ctrl+D when you're done.\n");
    }
    $counts = countWords(STDIN, $minLength);
} else {
    $counts = [];
    foreach ($files as $file) {
        $stream = is_file($file) && is_readable($file) ? fopen($file, 'r') : false;
        if ($stream === false) {
            quit("Can't read $file", 1);
        }
        foreach (countWords($stream, $minLength) as $word => $n) {
            $counts[$word] = ($counts[$word] ?? 0) + $n;
        }
        fclose($stream);
    }
    uksort($counts, fn ($a, $b) => [$counts[$b], $a] <=> [$counts[$a], $b]);
}

// Results on STDOUT, one per line, so other tools can take them apart.
foreach (array_slice($counts, 0, $top, true) as $word => $n) {
    printf("%5d  %s\n", $n, $word);
}
exit(0);
