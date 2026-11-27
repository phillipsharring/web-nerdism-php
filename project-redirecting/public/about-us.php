<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/redirect.php';

// The page moved, for good. 301 tells browsers and search engines to use the
// new address from now on.
redirect('/about.php', 301);
