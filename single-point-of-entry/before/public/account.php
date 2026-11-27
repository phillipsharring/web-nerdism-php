<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/auth.php';

header('Content-Type: text/plain; charset=utf-8');
echo 'Your account, ', currentUser()['name'], ".\n";
