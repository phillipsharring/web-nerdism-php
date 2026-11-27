<?php
declare(strict_types=1);

// A constant: a name for a value that never changes while the program runs.
const MAX_PEOPLE = 50;

// define() makes one too, and works anywhere, even inside an if.
define('APP_NAME', 'Bill Splitter');

class Bill
{
    // A class constant: it belongs to the class, the same for every bill.
    public const MIN_PEOPLE = 1;
}

// A static variable keeps its value between calls to the function.
function nextTicket(): int
{
    static $number = 0;
    return ++$number;
}

class Counter
{
    // A static property belongs to the class: one value, shared by every Counter.
    private static int $everyone = 0;

    // An ordinary property belongs to each object.
    private int $mine = 0;

    public function add(): void
    {
        self::$everyone++;
        $this->mine++;
    }

    public function mine(): int
    {
        return $this->mine;
    }

    // A static method is called on the class, not on an object, so it has no $this.
    public static function everyone(): int
    {
        return self::$everyone;
    }
}

echo APP_NAME, ': ', Bill::MIN_PEOPLE, ' to ', MAX_PEOPLE, " people\n";
echo 'Tickets: ', nextTicket(), ', ', nextTicket(), ', ', nextTicket(), "\n";

$a = new Counter();
$b = new Counter();
$a->add();
$a->add();
$b->add();
echo 'a counted ', $a->mine(), ', b counted ', $b->mine(), ', all counters together: ', Counter::everyone(), "\n";
