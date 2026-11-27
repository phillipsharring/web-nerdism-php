<?php

declare(strict_types=1);

// Every page, and who may see it. The URL picks a row; it never names a file.
return [
    '/' => ['page' => 'home', 'role' => null],
    '/account' => ['page' => 'account', 'role' => 'user'],
    '/admin' => ['page' => 'admin', 'role' => 'admin'],
];
