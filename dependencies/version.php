<?php
// Which version of Guzzle is installed? Composer records it, and can tell you.
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

echo 'Guzzle ', Composer\InstalledVersions::getPrettyVersion('guzzlehttp/guzzle'), "\n";
