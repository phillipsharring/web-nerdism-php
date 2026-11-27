<?php
$url = $argv[1] ?? 'https://example.com/';
$page = file_get_contents($url);

print_r(http_get_last_response_headers());
echo strlen($page), " bytes of page\n";
