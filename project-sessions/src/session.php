<?php
declare(strict_types=1);

// Start the session the same careful way on every page.
function start_session(): void
{
    session_start([
        'use_strict_mode' => true,      // refuse ids the server didn't make
        'cookie_httponly' => true,      // JavaScript can't read the id
        'cookie_samesite' => 'Lax',     // other sites can't post with it
        'cookie_secure' => ($_SERVER['HTTPS'] ?? 'off') !== 'off',
    ]);
}

// A message for the next page only: set it, redirect, show it once.
function flash(?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'] = $message;

        return null;
    }
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $message;
}

// Forget everything: the data, the file on the server, and the cookie.
function end_session(): void
{
    $_SESSION = [];
    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => $params['path'],
        'domain' => $params['domain'],
        'secure' => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => $params['samesite'],
    ]);
    session_destroy();
}
