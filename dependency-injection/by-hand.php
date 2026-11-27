<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\MemoryMailer;
use App\ScreenMailer;
use App\Welcome;

// Wiring by hand: make the pieces, hand each one what it needs.
$welcome = new Welcome(new ScreenMailer());
$welcome->send('ada@example.com', 'Ada');

// Same class, a different mailer. Welcome didn't change.
$memory = new MemoryMailer();
(new Welcome($memory))->send('grace@example.com', 'Grace');
echo 'Kept ', count($memory->sent), ' email, to ', $memory->sent[0]['to'], ". Nothing sent.\n";
