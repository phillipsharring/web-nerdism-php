<?php
declare(strict_types=1);

// Did this request arrive over HTTPS? Behind a proxy or CDN, PHP may never
// see HTTPS itself; then your proxy has to tell you (see What's in the Middle).
function is_https(): bool
{
    return ($_SERVER['HTTPS'] ?? 'off') !== 'off';
}

// Every cookie this site sets goes through here, so none goes out without
// its flags.
function set_cookie(string $name, string $value, int $seconds): void
{
    setcookie($name, $value, [
        'expires' => time() + $seconds,
        'path' => '/',
        'secure' => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function forget_cookie(string $name): void
{
    setcookie($name, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

// Never trust a cookie: it's input. Only a short, valid UTF-8 string counts.
function cookie_text(string $name, int $maxLength): string
{
    $value = $_COOKIE[$name] ?? '';
    if (!is_string($value) || !mb_check_encoding($value, 'UTF-8')) {
        return '';
    }
    $value = trim($value);

    return mb_strlen($value) <= $maxLength ? $value : '';
}
