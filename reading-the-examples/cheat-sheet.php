<?php
// Two slashes start a comment: a note for people. PHP skips it.
# A hash does the same.
/* And this kind can run
   over several lines. */

// A variable is a $ and a name. = puts a value in it.
// Every statement ends with a semicolon.
$name = 'Alice';
$count = 3;

// echo prints. "\n" starts a new line.
echo 'Hello', "\n";

// Double quotes fill in variables. Single quotes don't.
echo "Hello, $name\n";
echo 'Hello, $name', "\n";

// A dot joins strings together.
echo 'Hello, ' . $name . "\n";

// An array is a list, in square brackets. Counting starts at 0.
$sizes = ['small', 'medium', 'large'];
echo $sizes[0], "\n";

// Or a list of keys and values, with =>.
$shirt = ['size' => 'large', 'color' => 'blue'];
echo $shirt['color'], "\n";

// ?? means: this, or if it's missing, that.
echo $shirt['price'] ?? 'no price', "\n";

// Calling a function: its name, then what you give it, in ( ).
echo strtoupper($name), "\n";
echo count($sizes), "\n";

// if runs the block in { } only when the test is true.
if ($count > 2) {
    echo "More than two\n";
}

// foreach runs the block once for each item.
foreach ($sizes as $size) {
    echo "- $size\n";
}
