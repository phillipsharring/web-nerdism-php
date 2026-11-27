<?php
// The naive version: it stores md5($password). Don't copy this one.
// Run it, and it "signs up" the members, then writes the table to
// leaked.txt, the way it would look if someone copied your database.
declare(strict_types=1);

$users = require __DIR__ . '/users.php';

$rows = [];
foreach ($users as $email => $password) {
    $rows[] = $email . ' ' . md5($password);
}

file_put_contents(__DIR__ . '/leaked.txt', implode("\n", $rows) . "\n");
echo implode("\n", $rows), "\n";
