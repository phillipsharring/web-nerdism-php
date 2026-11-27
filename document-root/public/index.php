<?php
// public/ is the document root: only what's in here can be reached by a URL.
// The settings live one folder up, out of reach, and PHP reads them from there.
$settings = parse_ini_file(dirname(__DIR__) . '/.env');

header('Content-Type: text/plain');
echo $settings['GREETING'], "\n";
