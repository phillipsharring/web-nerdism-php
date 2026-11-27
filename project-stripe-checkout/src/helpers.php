<?php
declare(strict_types=1);

// Small functions every page uses.

/** 1250 cents as "$12.50", with integer math only. */
function money(int $cents): string
{
    return '$' . number_format(intdiv($cents, 100)) . '.' . str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value);
}

function startSession(): void
{
    session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax', 'use_strict_mode' => true]);
}

/** A token per session, in every form, checked on every POST. See CSRF. */
function csrfToken(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function checkCsrf(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrfToken(), $sent)) {
        http_response_code(403);
        exit("That form expired. Go back, reload, and try again.\n");
    }
}

/**
 * The Stripe secret key, if it's a test-mode key. This example refuses to run
 * with a live key, or with none.
 */
function stripeTestKey(): ?string
{
    $key = getenv('STRIPE_SECRET_KEY') ?: '';

    return preg_match('/^(sk|rk)_test_[A-Za-z0-9]+$/', $key) ? $key : null;
}
