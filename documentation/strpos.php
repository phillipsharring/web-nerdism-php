<?php
// The manual: strpos(string $haystack, string $needle, int $offset = 0): int|false
// It returns where it found the needle, counting from 0, or false if it didn't.
$text = 'apple pie';

var_dump(strpos($text, 'pie'));    // found at position 6
var_dump(strpos($text, 'apple'));  // found at position 0
var_dump(strpos($text, 'cherry')); // not found: false

// The check that skips the manual's warning: 0 counts as false, so it's wrong.
if (strpos($text, 'apple')) {
    echo "Loose check: found apple\n";
} else {
    echo "Loose check: no apple\n";
}

// The check the manual tells you to use.
if (strpos($text, 'apple') !== false) {
    echo "Strict check: found apple\n";
}

// Or, since PHP 8.0, the function made for the question.
if (str_contains($text, 'apple')) {
    echo "str_contains: found apple\n";
}
