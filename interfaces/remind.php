<?php
declare(strict_types=1);

require __DIR__ . '/remind-all.php';
require __DIR__ . '/ScreenNotifier.php';
require __DIR__ . '/MemoryNotifier.php';

$emails = ['ada@example.com', 'grace@example.com'];

// The same function, two kinds of notifier.
remindAll(new ScreenNotifier(), $emails);

$memory = new MemoryNotifier();
remindAll($memory, $emails);
echo 'Kept ', count($memory), " messages instead of sending them.\n";
