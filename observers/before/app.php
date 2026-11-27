<?php

declare(strict_types=1);

require __DIR__ . '/Signup.php';

$signup = new Signup();
$signup->register('alice@example.com', true);
$signup->register('bob@example.com', false);
