<?php
declare(strict_types=1);

/**
 * Asks for a user name and password with HTTP Basic authentication, and
 * returns the user name once they check out. Until then, it answers 401 and
 * stops, which makes the browser show its login box.
 *
 * @param array<string, string> $users user name => password hash
 */
function requireBasicAuth(array $users, string $realm): string
{
    // Never trust input. These come from the Authorization header, which
    // anyone can send with anything in it.
    $user = $_SERVER['PHP_AUTH_USER'] ?? '';
    $password = $_SERVER['PHP_AUTH_PW'] ?? '';

    // Check a hash even for an unknown name, so a wrong name takes as long as
    // a wrong password and the timing doesn't say which names exist.
    $hash = $users[$user] ?? '$2y$12$WLbaVZGMhGEcYrFgB8wtiuE27T2EZ.8Uj.86UBQdIWGPjff5a4UYu';
    $ok = password_verify($password, $hash) && isset($users[$user]);

    if (!$ok) {
        http_response_code(401);
        header('WWW-Authenticate: Basic realm="' . $realm . '", charset="UTF-8"');
        header('Content-Type: text/plain; charset=utf-8');
        echo "You need to log in to see this.\n";
        exit;
    }

    return $user;
}
