<?php
declare(strict_types=1);

// A token per session, sent with every form and checked on every POST, so
// another site can't make a logged-in browser submit our forms. See CSRF.

function startSession(): void
{
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

function csrfToken(): string
{
    $_SESSION['csrf'] ??= bin2hex(random_bytes(32));

    return $_SESSION['csrf'];
}

function checkCsrf(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrfToken(), $sent)) {
        http_response_code(403);
        exit("That form expired. Go back, reload, and try again.\n");
    }
}
