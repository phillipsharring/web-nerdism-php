<?php
declare(strict_types=1);

// One secret per session, made the first time a form needs it. Every form
// that changes something carries it; every request that changes something
// must bring it back.
function csrf_token(): string
{
    $_SESSION['csrf'] ??= bin2hex(random_bytes(32));

    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function csrf_check(): void
{
    $sent = $_POST['csrf'] ?? '';
    // hash_equals() takes the same time whether the first character is wrong
    // or the last, so the token can't be worked out a character at a time.
    if (!is_string($sent) || !isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $sent)) {
        http_response_code(403);
        exit("That form had expired. Go back, reload the page, and try again.\n");
    }
}
