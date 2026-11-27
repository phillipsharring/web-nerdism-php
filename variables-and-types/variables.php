<?php
$count = 3;
$price = 19.99;
$name = 'Alice';
$paid = true;

var_dump($count, $price, $name, $paid);

// The type belongs to the value. Assign something else, and the variable's type changes.
$count = 'three';
var_dump($count);

// Names are case-sensitive: $name and $Name are two different variables.
$Name = 'Bob';
echo $name, ' and ', $Name, "\n";

// PHP converts types when an operation needs them. That's called type juggling.
var_dump('5' + 5); // a number: int(10)
var_dump('5' . 5); // a string: "55"
