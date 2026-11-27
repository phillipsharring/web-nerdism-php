<?php

declare(strict_types=1);

// Declares a function, and does four other things while it's at it.
date_default_timezone_set('America/New_York');
ini_set('display_errors', '1');
file_put_contents(__DIR__ . '/visits.log', date('c') . "\n", FILE_APPEND);
echo "<nav><a href=\"/\">Home</a></nav>\n";

function slugify(string $title): string
{
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($title)), '-');
}
