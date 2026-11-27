<?php

declare(strict_types=1);

// A command-line job that only wants slugify().
require __DIR__ . '/helpers.php';

foreach (['Hello, World', 'Ten Tips for PHP'] as $title) {
    echo slugify($title), "\n";
}
