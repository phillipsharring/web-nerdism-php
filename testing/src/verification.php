<?php
declare(strict_types=1);

// The piece of a signup that makes and checks the "verify your email" link,
// pulled out on its own: no database, no mail server, no web page. Just two functions.

function verificationLink(string $email, int $expiresAt, string $secret, string $baseUrl): string
{
    $signature = hash_hmac('sha256', "{$email}|{$expiresAt}", $secret);

    return $baseUrl . '/verify?' . http_build_query([
        'email' => $email,
        'expires' => $expiresAt,
        'sig' => $signature,
    ]);
}

// Returns the email address if the link is genuine and not expired, or null.
function verifiedEmail(string $url, int $now, string $secret): ?string
{
    // Never trust input: the link comes back in a request, and anyone can edit it.
    parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
    $email = $query['email'] ?? null;
    $expires = $query['expires'] ?? null;
    $signature = $query['sig'] ?? null;
    if (!is_string($email) || !is_string($expires) || !is_string($signature) || !ctype_digit($expires)) {
        return null;
    }

    $expected = hash_hmac('sha256', "{$email}|{$expires}", $secret);
    if (!hash_equals($expected, $signature)) {
        return null;
    }
    if ((int) $expires < $now) {
        return null;
    }

    return $email;
}
