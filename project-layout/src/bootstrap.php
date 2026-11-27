<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$config = require __DIR__ . '/config.php';

date_default_timezone_set($config['timezone']);
