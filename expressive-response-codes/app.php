<?php

declare(strict_types=1);

// Pretend data, so the example needs no database. The passwords are
// "correct horse battery staple" (Alice) and "tr0ub4dor" (Bob).
const USERS = [
    1 => ['email' => 'alice@example.com', 'hash' => '$2y$12$lZeFITUoUPwxWX5M43HvCexbe4Ndijd6nxKVY7y/37eE7F0UYknze'],
    2 => ['email' => 'bob@example.com', 'hash' => '$2y$12$DX1DD4t1ta2kyGdnqzQ4geGhWuOmyrmdSgFQ6vQoQPAhHbEGycU7O'],
];
const TOKENS = ['alice-token' => 1, 'bob-token' => 2];
const NOTES = [
    1 => ['owner' => 1, 'text' => 'Buy milk'],
    2 => ['owner' => 2, 'text' => 'Call the bank'],
];

function respond(int $status, array $body, array $headers = []): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    foreach ($headers as $name => $value) {
        header("$name: $value");
    }
    echo json_encode($body), "\n";
    exit;
}

function userIdByEmail(string $email): ?int
{
    foreach (USERS as $id => $user) {
        if ($user['email'] === $email) {
            return $id;
        }
    }
    return null;
}

// Never trust input: the header is whatever the client sent.
function currentUserId(): ?int
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!str_starts_with($header, 'Bearer ')) {
        return null;
    }
    return TOKENS[substr($header, 7)] ?? null;
}

function postedString(string $name): string
{
    return is_string($_POST[$name] ?? null) ? trim($_POST[$name]) : '';
}
