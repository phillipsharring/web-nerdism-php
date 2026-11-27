<?php
header('Content-Type: text/plain');

// How PHP is running: fpm-fcgi here, cli-server under php -S.
echo 'PHP is running as: ', PHP_SAPI, "\n";

// The worker that took this request. Ask a few times.
echo 'Worker process:    ', getmypid(), "\n";

// What nginx told PHP about the request.
echo 'Script:            ', $_SERVER['SCRIPT_FILENAME'], "\n";
echo 'Web server:        ', $_SERVER['SERVER_SOFTWARE'], "\n";
