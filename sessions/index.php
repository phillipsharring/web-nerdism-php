<?php
declare(strict_types=1);

// Reads the session id from the visitor's cookie (or makes a new one),
// then loads that visitor's saved data from a file into $_SESSION.
session_start([
    'use_strict_mode' => true, // Never trust input: refuse any id this server didn't make.
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);

$_SESSION['visits'] = ($_SESSION['visits'] ?? 0) + 1;

header('Content-Type: text/plain; charset=utf-8');
echo 'Visit number ', $_SESSION['visits'], ".\n";
echo 'Session id: ', session_id(), "\n";
echo 'Saved in: ', session_save_path() ?: sys_get_temp_dir(), '/sess_', session_id(), "\n";
