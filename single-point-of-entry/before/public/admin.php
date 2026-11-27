<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';
// Someone forgot auth.php here. Nothing complains.

header('Content-Type: text/plain; charset=utf-8');
echo "Admin: today's orders, every customer's email address.\n";
