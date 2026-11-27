<?php
declare(strict_types=1);

function start_session(): void
{
    session_start([
        'use_strict_mode' => true,      // refuse ids the server didn't make
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

// The signed-in member's id, or off to the sign-in page.
function require_login(): int
{
    if (!isset($_SESSION['user_id'])) {
        redirect('/login.php');
    }
    header('Cache-Control: private, no-store');

    return $_SESSION['user_id'];
}
