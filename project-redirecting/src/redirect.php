<?php
declare(strict_types=1);

// Send the browser somewhere else, and stop. Without the exit, the rest of
// the page still runs, and is still sent.
function redirect(string $to, int $status = 303): never
{
    header('Location: ' . $to, true, $status);
    exit;
}

// Never trust input. A "where to go next" value from the request is only
// used if it's a path on this site: one slash, then anything but a second
// slash or a backslash, and no control characters. Anything else gets the
// fallback.
function local_path(mixed $value, string $fallback = '/'): string
{
    if (!is_string($value) || !str_starts_with($value, '/')) {
        return $fallback;
    }
    if (str_starts_with($value, '//') || str_contains($value, '\\') || preg_match('/[\x00-\x1F\x7F]/', $value)) {
        return $fallback;
    }

    return $value;
}

function theme(): string
{
    $theme = $_COOKIE['theme'] ?? '';

    return in_array($theme, ['light', 'dark'], true) ? $theme : 'light';
}
