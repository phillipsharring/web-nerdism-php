<?php

declare(strict_types=1);

require __DIR__ . '/Mailer.php';
require __DIR__ . '/Signup.php';

function check(string $name, bool $passed): void
{
    echo $passed ? 'ok    ' : 'FAIL  ', $name, "\n";
}

// Test 1: signing up sends one email.
(new Signup())->register('alice@example.com');
check('alice gets one welcome email', count(Mailer::getInstance()->sent()) === 1);

// Test 2: the same, for someone else. Same code, same expectation.
(new Signup())->register('bob@example.com');
check('bob gets one welcome email', count(Mailer::getInstance()->sent()) === 1);
