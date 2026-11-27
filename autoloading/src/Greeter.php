<?php
declare(strict_types=1);

// App\Greeter lives in src/Greeter.php: the App\ prefix means src/.
namespace App;

use App\Text\Shout;

class Greeter
{
    private Shout $shout;

    public function __construct(Shout $shout)
    {
        $this->shout = $shout;
    }

    public function greet(string $name): string
    {
        return $this->shout->loud("Hello, {$name}") . "\n";
    }
}
