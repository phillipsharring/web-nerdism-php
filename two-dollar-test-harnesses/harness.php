<?php
declare(strict_types=1);

// A two-dollar test harness: load one file, call it, check the answers.
require __DIR__ . '/src/verification.php';

$secret = 'not-a-real-secret';
$now = 1_800_000_000; // A fixed time, so every run gives the same answers.
$link = verificationLink('ada@example.com', $now + 3600, $secret, 'https://example.com');
echo $link, "\n";

$checks = [
    'a fresh link is good' => verifiedEmail($link, $now, $secret) === 'ada@example.com',
    'a changed email is refused' => verifiedEmail(str_replace('ada', 'eve', $link), $now, $secret) === null,
    'an expired link is refused' => verifiedEmail($link, $now + 7200, $secret) === null,
    'another secret is refused' => verifiedEmail($link, $now, 'some-other-secret') === null,
    'junk is refused' => verifiedEmail('https://example.com/verify?email[]=x', $now, $secret) === null,
];

$failed = 0;
foreach ($checks as $name => $ok) {
    echo $ok ? 'ok    ' : 'FAIL  ', $name, "\n";
    $failed += $ok ? 0 : 1;
}
exit($failed === 0 ? 0 : 1);
