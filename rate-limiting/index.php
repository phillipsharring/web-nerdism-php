<?php
// The fixed version: count failed logins, and stop answering when there are
// too many: 10 per email address, or 50 per IP address, in 15 minutes.
declare(strict_types=1);

const MAX_PER_EMAIL = 10;
const MAX_PER_IP = 50;
const WINDOW_MINUTES = 15;

$pdo = require __DIR__ . '/db.php';
require __DIR__ . '/members.php';

header('Content-Type: text/plain; charset=utf-8');

$email = is_string($_POST['email'] ?? null) ? $_POST['email'] : '';
// The visitor's address. Behind a proxy or CDN, this is the proxy's address;
// see What's in the Middle for which one to use instead.
$ip = $_SERVER['REMOTE_ADDR'];

function recentFailures(PDO $pdo, string $column, string $value): int
{
    // $column is one of two names written in this file, never input.
    $statement = $pdo->prepare(
        "SELECT COUNT(*) FROM `login_failures`
         WHERE `$column` = ? AND `failed_at` > NOW() - INTERVAL " . WINDOW_MINUTES . ' MINUTE'
    );
    $statement->execute([$value]);
    return (int) $statement->fetchColumn();
}

// Check the limits before the password, so a locked-out guess learns nothing,
// not even whether it was right.
if (recentFailures($pdo, 'email', $email) >= MAX_PER_EMAIL || recentFailures($pdo, 'ip', $ip) >= MAX_PER_IP) {
    http_response_code(429);
    header('Retry-After: ' . WINDOW_MINUTES * 60);
    exit("Too many failed logins. Try again in " . WINDOW_MINUTES . " minutes.\n");
}

if (passwordIsRight($pdo, $email, $_POST['password'] ?? null)) {
    $pdo->prepare('DELETE FROM `login_failures` WHERE `email` = ?')->execute([$email]);
    echo "Welcome back.\n";
} else {
    $pdo->prepare('INSERT INTO `login_failures` (`email`, `ip`) VALUES (?, ?)')->execute([$email, $ip]);
    http_response_code(401);
    echo "Wrong email or password.\n";
}
