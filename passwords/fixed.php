<?php
// The fixed version: password_hash() to store, password_verify() to check.
// It writes leaked-fixed.txt, the same leak from a site that did it right.
declare(strict_types=1);

$users = require __DIR__ . '/users.php';

$rows = [];
foreach ($users as $email => $password) {
    $rows[] = $email . ' ' . password_hash($password, PASSWORD_DEFAULT);
}

file_put_contents(__DIR__ . '/leaked-fixed.txt', implode("\n", $rows) . "\n");
echo implode("\n", $rows), "\n\n";

// Logging in: hash what they typed the same way, with the salt from the stored hash.
[, $aliceHash] = explode(' ', $rows[0], 2);
echo 'Alice types "sunshine": ', password_verify('sunshine', $aliceHash) ? 'yes' : 'no', "\n";
echo 'Alice types "Sunshine": ', password_verify('Sunshine', $aliceHash) ? 'yes' : 'no', "\n";

// Years later, the default gets stronger. At login, while you have the
// password, check whether its hash is out of date, and if so, make a new one.
$old = password_hash('sunshine', PASSWORD_BCRYPT, ['cost' => 10]);
echo 'A cost-10 hash needs rehashing: ', password_needs_rehash($old, PASSWORD_DEFAULT) ? 'yes' : 'no', "\n";
