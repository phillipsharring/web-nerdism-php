<?php
$price = 1999;
$tags = ['php', 'web'];
$found = false;

echo 'echo prints strings: ', $price, "\n";
print "print does the same, one thing at a time\n";
printf("printf fills in a pattern: %s costs %d cents\n", 'A shirt', $price);

$padded = sprintf('%05d', 42); // sprintf returns the string instead of printing it
echo 'sprintf made: ', $padded, "\n";

echo 'echo false prints: [', $found, "]\n";

echo "print_r, for people:\n";
print_r($tags);

echo "var_dump, with the types:\n";
var_dump($found, $tags);

echo "var_export, as PHP you could paste:\n";
var_export($tags);
echo "\n";
