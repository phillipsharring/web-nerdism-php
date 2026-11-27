<?php
// Shared by both versions: a session, with a pretend logged-in member.
declare(strict_types=1);

session_start();
$_SESSION['email'] ??= 'alice@example.com';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Never trust input: the new address has to be one.
function newEmail(): ?string
{
    $email = $_POST['email'] ?? null;
    return is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false ? $email : null;
}
