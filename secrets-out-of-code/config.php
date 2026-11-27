<?php
// The fixed version: the code knows the names of its secrets, never the values.
// The values arrive in the environment, from outside the repository.
declare(strict_types=1);

function secret(string $name): string
{
    $value = getenv($name);
    if ($value === false || $value === '') {
        // Fail at once, and loudly. Never fall back to a default secret.
        throw new RuntimeException("$name is not set. See .env.example.");
    }
    return $value;
}

return [
    'payment_api_key' => secret('PAYMENT_API_KEY'),
    'db_password' => secret('DB_PASSWORD'),
];
