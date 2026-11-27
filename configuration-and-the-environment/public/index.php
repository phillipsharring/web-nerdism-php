<?php
// Settings come from the environment, not the code. When one is missing,
// fall back to the safest choice: assume production.
$env = getenv('APP_ENV') ?: 'production';
$name = getenv('APP_NAME') ?: 'Untitled';
$key = getenv('MAIL_API_KEY');

// Show errors while you work; never on a live site.
ini_set('display_errors', $env === 'development' ? '1' : '0');

header('Content-Type: text/plain');

// A setting the app can't work without stops it, loudly, at the start.
if ($key === false || $key === '') {
    http_response_code(500);
    error_log('MAIL_API_KEY is not set.');
    echo "This app isn't set up yet.\n";
    exit;
}

echo "$name, running in $env.\n";

// Never print a secret. Say that it's there, and no more than its last few characters.
echo 'Mail key: set, ending in ', substr($key, -4), "\n";
