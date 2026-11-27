<?php
declare(strict_types=1);

function start_session(): void
{
    session_start([
        'use_strict_mode' => true,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'cookie_secure' => ($_SERVER['HTTPS'] ?? 'off') !== 'off',
    ]);
}

function redirect(string $to): never
{
    header('Location: ' . $to, true, 303);
    exit;
}

// From the Redirecting project: only a path on this site.
function local_path(mixed $value, string $fallback = '/'): string
{
    if (!is_string($value) || !str_starts_with($value, '/') || str_starts_with($value, '//')
        || str_contains($value, '\\') || preg_match('/[\x00-\x1F\x7F]/', $value)) {
        return $fallback;
    }

    return $value;
}

function current_user(): ?string
{
    return $_SESSION['user'] ?? null;
}

// The guard. One line at the top of every protected page.
function require_login(): string
{
    $user = current_user();
    if ($user === null) {
        redirect('/login.php?next=' . rawurlencode($_SERVER['REQUEST_URI'] ?? '/'));
    }

    // Pages for one person must never be kept by a cache in the middle.
    header('Cache-Control: private, no-store');

    return $user;
}

function sign_in(string $email): void
{
    // A new id at the moment someone becomes someone. See Login Form.
    session_regenerate_id(true);
    $_SESSION['user'] = $email;
}

function sign_out(): void
{
    $_SESSION = [];
    $p = session_get_cookie_params();
    setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => $p['path'], 'domain' => $p['domain'],
        'secure' => $p['secure'], 'httponly' => $p['httponly'], 'samesite' => $p['samesite']]);
    session_destroy();
}
