<?php
declare(strict_types=1);

// Try a list of common passwords against an md5 hash. Real attackers use
// lists of billions, and graphics cards; this is the idea in ten lines.
$hash = $argv[1] ?? '';
$words = file(__DIR__ . '/src/common-passwords.txt', FILE_IGNORE_NEW_LINES);
$words = [...$words, 'password', 'password1', 'password123', 'qwerty', 'letmein', '123456'];

foreach ($words as $word) {
    if (md5($word) === $hash) {
        echo "Found it: $word\n";
        exit;
    }
}
echo "Not in this list.\n";
