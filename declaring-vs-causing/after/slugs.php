<?php

declare(strict_types=1);

// The same job, now requiring only what declares.
require __DIR__ . '/text.php';

foreach (['Hello, World', 'Ten Tips for PHP'] as $title) {
    echo slugify($title), "\n";
}
