<?php
declare(strict_types=1);

// A list: PHP numbers the items for you, from 0.
$sizes = ['small', 'medium', 'large'];
$sizes[] = 'extra large'; // [] on the end adds one
echo $sizes[0], ', ', count($sizes), " sizes\n";

// Named keys: a set of values that belong together, like one row of data.
$order = [
    'id' => 1001,
    'email' => 'alice@example.com',
    'items' => ['shirt', 'mug'], // an array inside an array
];
echo 'Order ', $order['id'], ' has ', count($order['items']), " items\n";

// foreach walks through, with or without the keys.
foreach ($order as $key => $value) {
    echo $key, ': ', is_array($value) ? implode(', ', $value) : $value, "\n";
}

// Is it there? isset() says no to a null value; array_key_exists() doesn't care.
$settings = ['theme' => null];
var_dump(isset($settings['theme']), array_key_exists('theme', $settings));

// in_array() with true compares strictly.
var_dump(in_array('0', [0, 1], false), in_array('0', [0, 1], true));

// Keys that look like whole numbers become ints. Others stay strings.
var_dump(array_keys(['7' => 'a', '07' => 'b', '7.5' => 'c']));

// Making new arrays from old ones.
$prices = ['shirt' => 2000, 'mug' => 1200, 'sticker' => 300];
$cheap = array_filter($prices, fn(int $cents): bool => $cents < 1500);
$doubled = array_map(fn(int $cents): int => $cents * 2, $prices);
print_r($cheap);
print_r($doubled);

// sort() changes the array itself, and returns true, not the sorted array.
$names = ['Carol', 'alice', 'Bob'];
sort($names);
print_r($names); // capitals first: plain sort compares bytes
sort($names, SORT_FLAG_CASE | SORT_STRING);
print_r($names);

// Arrays are copied when you assign them.
$copy = $names;
$copy[] = 'Dave';
echo count($names), ' and ', count($copy), "\n";
