<?php
// The naive version: as many guesses as anyone cares to make. Don't copy this one.
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';
require __DIR__ . '/members.php';

header('Content-Type: text/plain; charset=utf-8');

if (passwordIsRight($pdo, $_POST['email'] ?? null, $_POST['password'] ?? null)) {
    echo "Welcome back.\n";
} else {
    http_response_code(401);
    echo "Wrong email or password.\n";
}
