<?php
declare(strict_types=1);

// Settings for the whole site, in one place. Not secret ones: those go in
// the environment (see Configuration and the Environment).
return [
    'site_name' => 'Alex Example',
    'timezone' => 'UTC',
    'nav' => [
        '/' => 'Home',
        '/about.php' => 'About',
    ],
];
