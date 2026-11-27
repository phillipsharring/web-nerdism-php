<?php
$visits = (int) ($_COOKIE['visits'] ?? 0) + 1;

setcookie('visits', (string) $visits, [
    'expires' => time() + 3600,
    'httponly' => true,
    'samesite' => 'Lax',
]);

echo "Visit number $visits.\n";
