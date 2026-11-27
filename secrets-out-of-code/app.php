<?php
declare(strict_types=1);

$config = require __DIR__ . '/config.php';

// Prove which key is loaded without printing it: the last four characters only.
echo 'Payment key loaded: ...', substr($config['payment_api_key'], -4), "\n";
