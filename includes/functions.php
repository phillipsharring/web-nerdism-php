<?php
declare(strict_types=1);

// Escape text for HTML. Short, because it's used everywhere.
function e(string $text): string
{
    return htmlspecialchars($text);
}
