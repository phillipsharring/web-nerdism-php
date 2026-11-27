<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/auth.php';

// Signing out changes something, so it's a POST, never a link.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

start_session();
sign_out();
redirect('/');
