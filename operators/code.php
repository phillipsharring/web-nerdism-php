<?php
declare(strict_types=1);

// A stored one-time code. This one happens to look like a number:
// 0e followed by digits is 0 times ten to some power, which is 0.
$expected = '0e462097431906509019562988736854';

// Never trust input: it's a string or it's nothing.
$code = is_string($_GET['code'] ?? null) ? $_GET['code'] : '';

header('Content-Type: text/plain');
echo '==  says: ', $code == $expected ? 'let in' : 'refused', "\n";
echo '=== says: ', $code === $expected ? 'let in' : 'refused', "\n";
