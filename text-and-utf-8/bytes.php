<?php
declare(strict_types=1);

$word = 'café';

// strlen counts bytes. é takes two in UTF-8, so four letters are five bytes.
echo 'strlen: ', strlen($word), ', mb_strlen: ', mb_strlen($word), "\n";
echo 'The bytes: ', bin2hex($word), "\n"; // c3a9 at the end is é

// Byte functions can cut a letter in half. The mb_ functions count characters.
echo 'substr: ', bin2hex(substr($word, 0, 4)), ', mb_substr: ', mb_substr($word, 0, 4), "\n";
echo 'strtoupper: ', strtoupper($word), ', mb_strtoupper: ', mb_strtoupper($word), "\n";

// One emoji is one character, and four bytes.
echo 'thumbs up: ', strlen('👍'), ' bytes, ', mb_strlen('👍'), " character\n";

// The same é can be one code point, or an e plus a combining accent. They look the same.
$combined = "cafe\u{0301}";
echo $combined, ' is ', mb_strlen($combined), ' characters; equal to café? ', $combined === $word ? 'yes' : 'no', "\n";

// Mojibake: UTF-8 bytes read as if they were Latin-1.
echo 'Read wrong: ', mb_convert_encoding($word, 'UTF-8', 'ISO-8859-1'), "\n";

// Is it valid UTF-8 at all? Input might not be.
var_dump(mb_check_encoding($word, 'UTF-8'), mb_check_encoding("caf\xE9", 'UTF-8'));
