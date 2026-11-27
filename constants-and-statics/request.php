<?php
declare(strict_types=1);

function nextTicket(): int
{
    static $number = 0;
    return ++$number;
}

// Each request starts PHP fresh, so every visitor gets ticket 1.
header('Content-Type: text/plain; charset=utf-8');
echo 'Your ticket: ', nextTicket(), "\n";
