<?php
declare(strict_types=1);

// == compares after juggling the types. === compares the types too.
var_dump(1 == '1');    // true
var_dump(1 === '1');   // false: int and string
var_dump('1' == '01'); // true: two numeric strings are compared as numbers
var_dump('10' == '1e1'); // true: 1e1 is 10, in scientific notation
var_dump(null == false); // true
var_dump(0 == '');     // false, since PHP 8. It was true before.

// <=> says which side is bigger: -1, 0 or 1.
var_dump(5 <=> 3);

// ?? gives the left side unless it's missing or null. ?: unless it's false-ish.
$settings = ['name' => '', 'color' => null];
var_dump($settings['color'] ?? 'blue'); // 'blue'
var_dump($settings['size'] ?? 'large'); // 'large', with no warning
var_dump($settings['name'] ?? 'Guest'); // '' is set, so ''
var_dump($settings['name'] ?: 'Guest'); // '' counts as false, so 'Guest'

// The ternary: condition ? if true : if false.
$items = 3;
echo $items === 1 ? 'item' : 'items', "\n";

// and/or bind looser than =. Use && and || instead.
$loose = false or true;
$tight = false || true;
var_dump($loose, $tight); // false, true
