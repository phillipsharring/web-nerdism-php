<?php
// The attacker's side: guess passwords against a leaked table, for five
// seconds. Every word in words.txt, then every word with 0 to 9999 after it.
declare(strict_types=1);

$file = $argv[1] ?? '';
if (!is_file($file)) {
    exit("Usage: php crack.php leaked.txt\n");
}

$hashes = [];
foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    [$email, $hash] = explode(' ', $line, 2);
    $hashes[$email] = $hash;
}
$words = file(__DIR__ . '/words.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

function guesses(array $words): Generator
{
    yield from $words;
    foreach ($words as $word) {
        for ($n = 0; $n <= 9999; $n++) {
            yield $word . $n;
        }
    }
}

$start = microtime(true);
$tries = 0;
$cracked = [];

foreach (guesses($words) as $guess) {
    if (microtime(true) - $start > 5 || count($cracked) === count($hashes)) {
        break;
    }
    $md5 = md5($guess); // one hash of the guess, checked against every md5 row
    foreach ($hashes as $email => $hash) {
        if (isset($cracked[$email])) {
            continue;
        }
        $tries++;
        // An md5 row is compared directly. A password_hash() row has its own
        // salt, so every guess has to be hashed again for every row.
        $match = str_starts_with($hash, '$') ? password_verify($guess, $hash) : hash_equals($hash, $md5);
        if ($match) {
            $cracked[$email] = $guess;
            echo "Cracked $email: $guess\n";
        }
    }
}

$seconds = microtime(true) - $start;
printf("%s guesses in %.1f seconds. Cracked %d of %d.\n",
    number_format($tries), $seconds, count($cracked), count($hashes));
