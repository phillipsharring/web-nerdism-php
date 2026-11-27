<?php
declare(strict_types=1);

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// A web address, or nothing. Escaping can't make javascript:alert(1) safe in
// an href, because it has nothing to escape. Only the scheme check can.
function web_url(string $url): string
{
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        return '';
    }
    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

    return in_array($scheme, ['http', 'https'], true) ? $url : '';
}
