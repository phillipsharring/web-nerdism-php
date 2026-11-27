<?php
declare(strict_types=1);

// Makes a new API token for a user, stores its hash, and prints the token.
// It's shown this once: lose it, and you make a new one.
//   php bin/make-token.php ann@example.com "My script" [--write]

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require dirname(__DIR__) . '/src/db.php';

[, $email, $name] = $argv + [null, null, null];
if (!is_string($email) || !is_string($name) || $name === '') {
    fwrite(STDERR, "Usage: php bin/make-token.php EMAIL NAME [--write]\n");
    exit(2);
}

$db = connect();
$stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
$userId = $stmt->fetchColumn();
if ($userId === false) {
    fwrite(STDERR, "No user with that email.\n");
    exit(1);
}

$token = 'wnp_' . bin2hex(random_bytes(20));
$db->prepare('INSERT INTO api_tokens (user_id, token_hash, name, can_write) VALUES (?, ?, ?, ?)')
    ->execute([$userId, hash('sha256', $token), $name, in_array('--write', $argv, true) ? 1 : 0]);
echo $token, "\n";
