<?php
// The fixed version: the same page, with the headers.
declare(strict_types=1);

require __DIR__ . '/headers.php';
sendSecurityHeaders();

require __DIR__ . '/page.php';
