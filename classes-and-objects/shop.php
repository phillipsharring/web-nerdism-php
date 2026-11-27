<?php
declare(strict_types=1);

require __DIR__ . '/Cart.php';

// Two objects from one class. Each has its own items.
$mine = new Cart('USD');
$mine->add('T-shirt', 1500, 2);
$mine->add('Sticker', 250);

$yours = new Cart('USD');
$yours->add('Mug', 1200);

echo 'Mine:  ', $mine->count(), ' items, ', $mine->formattedTotal(), "\n";
echo 'Yours: ', $yours->count(), ' item, ', $yours->formattedTotal(), "\n";

// A variable holds a handle to an object, not a copy of it.
$alsoMine = $mine;
$alsoMine->add('Hat', 2000);
echo 'Mine, after adding a hat to $alsoMine: ', $mine->formattedTotal(), "\n";

// clone makes an actual copy.
$copy = clone $mine;
$copy->add('Socks', 800);
echo 'Mine, after adding socks to the copy:  ', $mine->formattedTotal(), "\n";
