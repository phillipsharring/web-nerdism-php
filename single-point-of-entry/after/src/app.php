<?php

declare(strict_types=1);

// Declares only. Pretend logins, so the example needs no database or sessions.
const TOKENS = [
    'alice-token' => ['name' => 'Alice', 'role' => 'user'],
    'root-token' => ['name' => 'Ada', 'role' => 'admin'],
];

// Never trust input: the header is whatever the client sent.
function currentUser(): ?array
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    return str_starts_with($header, 'Bearer ') ? TOKENS[substr($header, 7)] ?? null : null;
}
