<?php
declare(strict_types=1);

class Clock
{
    public static function hour(): int
    {
        return (int) date('G');
    }
}

// The static call is welded in: this always asks the real clock. To see what it
// says in the afternoon, you have to wait for the afternoon.
class WeldedGreeting
{
    public function text(): string
    {
        return Clock::hour() < 12 ? 'Good morning' : 'Good afternoon';
    }
}

// The same thing with the hour handed in. Any hour you like, whenever you like.
class Greeting
{
    public function text(int $hour): string
    {
        return $hour < 12 ? 'Good morning' : 'Good afternoon';
    }
}

echo 'Welded, right now: ', (new WeldedGreeting())->text(), "\n";

$greeting = new Greeting();
foreach ([9, 15] as $hour) {
    echo "Handed {$hour}: ", $greeting->text($hour), "\n";
}
echo 'Handed the real hour: ', $greeting->text(Clock::hour()), "\n";
