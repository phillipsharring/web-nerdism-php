<?php
declare(strict_types=1);

// App\Text\Shout lives in src/Text/Shout.php: one folder per namespace part.
namespace App\Text;

class Shout
{
    public function loud(string $text): string
    {
        return mb_strtoupper($text) . '!';
    }
}
