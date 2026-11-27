<?php
declare(strict_types=1);

// Every page starts by requiring this file, and gets everything below.
require_once __DIR__ . '/functions.php';

$config = require __DIR__ . '/config.php';

date_default_timezone_set($config['timezone']);
