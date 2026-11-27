<?php
declare(strict_types=1);

const MIN_SECONDS = 3;          // nobody reads a post and writes a comment faster
const MAX_SECONDS = 60 * 60;    // a form left open for an hour gets a fresh token
const MAX_PER_TEN_MINUTES = 5;  // per address
const MAX_LINKS = 1;            // more than this, and a person looks first

/** A token for the form: when it was shown, signed so nobody can forge the time. */
function formToken(int $postId, string $secret): string
{
    $time = time();

    return $time . '.' . hash_hmac('sha256', "$postId|$time", $secret);
}

/** Was this form shown by us, for this post, long enough ago, and not too long? */
function tokenIsGood(mixed $token, int $postId, string $secret): bool
{
    if (!is_string($token) || !preg_match('/^(\d{1,12})\.([a-f0-9]{64})$/', $token, $m)) {
        return false;
    }
    $age = time() - (int) $m[1];

    return hash_equals(hash_hmac('sha256', "$postId|$m[1]", $secret), $m[2])
        && $age >= MIN_SECONDS && $age <= MAX_SECONDS;
}

/** Has this address commented too often lately? */
function tooMany(PDO $db, string $ip): bool
{
    $stmt = $db->prepare(
        'SELECT COUNT(*) FROM comments WHERE ip = ? AND created_at > NOW() - INTERVAL 10 MINUTE'
    );
    $stmt->execute([$ip]);

    return (int) $stmt->fetchColumn() >= MAX_PER_TEN_MINUTES;
}

/** Links are what spam is for. A comment with several waits for a person. */
function needsALook(string $body): bool
{
    return preg_match_all('#https?://|www\.#i', $body) > MAX_LINKS;
}
