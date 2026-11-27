<?php
declare(strict_types=1);

// Runs a template and hands back what it printed, as a string.
function render(string $template, array $vars): string
{
    ob_start();
    require $template;
    return ob_get_clean();
}

// Never trust input: a name from the query string is whatever anyone typed.
$name = is_string($_GET['name'] ?? null) ? trim($_GET['name']) : '';

$html = render(dirname(__DIR__) . '/templates/greeting.php', [
    'name' => $name === '' ? 'stranger' : $name,
]);

// The whole page is in $html now, so we know its size before sending a byte.
header('Content-Type: text/html; charset=utf-8');
header('Content-Length: ' . strlen($html));
echo $html;
