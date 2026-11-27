<?php
declare(strict_types=1);

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function nav_link(string $href, string $label, string $current): string
{
    $mark = $href === $current ? ' aria-current="page"' : '';

    return '<a href="' . e($href) . '"' . $mark . '>' . e($label) . '</a>';
}

// Run a template with the given variables, and return what it printed,
// instead of sending it. The template name comes from your code, never
// from the request.
function render(string $template, array $vars = []): string
{
    extract($vars, EXTR_SKIP);
    ob_start();
    require dirname(__DIR__) . '/templates/' . $template . '.php';

    return (string) ob_get_clean();
}
