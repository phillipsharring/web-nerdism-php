<?php
header('Content-Type: text/plain');

echo 'Connected to PHP from:  ', $_SERVER['REMOTE_ADDR'], "\n";
echo 'X-Forwarded-For:        ', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '(none)', "\n";
echo 'Host:                   ', $_SERVER['HTTP_HOST'], "\n";
