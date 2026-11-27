<?php

declare(strict_types=1);

// Declares. Requiring this file changes nothing.

function slugify(string $title): string
{
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($title)), '-');
}
