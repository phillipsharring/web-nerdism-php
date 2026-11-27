<?php
// Shared by both versions: one pretend member, and a login check.
// The password is "correct horse battery staple".
declare(strict_types=1);

const MEMBERS = [
    'alice@example.com' => '$2y$12$HEA59rdH50QDhKzo1h3G7Oxt0X.q59l7rP5Jio4u/kbNJ2Xd3TO4i',
];

// Never trust input: both fields must be strings, and the password must match.
function checkLogin(): ?string
{
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    if (!is_string($email) || !is_string($password) || !isset(MEMBERS[$email])) {
        return null;
    }
    return password_verify($password, MEMBERS[$email]) ? $email : null;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
