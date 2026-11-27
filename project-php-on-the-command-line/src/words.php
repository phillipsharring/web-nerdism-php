<?php
declare(strict_types=1);

/**
 * Counts the words in a stream of text, line by line, so a file of any size
 * fits in memory. A word is a run of letters, digits and apostrophes, in any
 * language; "Café" and "café" are the same word.
 *
 * @param resource $stream
 * @return array<string, int> word => count, most common first
 */
function countWords($stream, int $minLength): array
{
    $counts = [];
    while (($line = fgets($stream)) !== false) {
        foreach (preg_split("/[^\\p{L}\\p{N}']+/u", mb_strtolower($line), -1, PREG_SPLIT_NO_EMPTY) as $word) {
            $word = trim($word, "'");
            if (mb_strlen($word) >= $minLength) {
                $counts[$word] = ($counts[$word] ?? 0) + 1;
            }
        }
    }
    // Most common first; ties in alphabetical order, so the output is the same every run.
    uksort($counts, fn ($a, $b) => [$counts[$b], $a] <=> [$counts[$a], $b]);

    return $counts;
}
