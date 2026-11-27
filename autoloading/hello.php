<?php
declare(strict_types=1);

// One require, for every class in the project and every package it uses.
require __DIR__ . '/vendor/autoload.php';

use App\Greeter;
use App\Text\Shout;

$greeter = new Greeter(new Shout());
echo $greeter->greet('Ada');
echo $greeter->greet('Zoë');
