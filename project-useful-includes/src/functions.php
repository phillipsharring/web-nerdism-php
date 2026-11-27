<?php
declare(strict_types=1);

// Escape text for HTML. Short, because you'll type it a hundred times.
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// A navigation link, marked as the current page when it is one.
function nav_link(string $href, string $label, string $current): string
{
    $mark = $href === $current ? ' aria-current="page"' : '';

    return '<a href="' . e($href) . '"' . $mark . '>' . e($label) . '</a>';
}
