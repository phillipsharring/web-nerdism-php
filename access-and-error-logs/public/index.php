<?php
// Every request comes here. Both servers log it before this code ever runs.
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/') {
    echo "Hello. Now go and read the logs.\n";
} elseif ($path === '/broken') {
    // A bug, on purpose: $name was never set. The visitor sees a page;
    // the error log gets the details.
    echo "Hello, $name.\n";
} else {
    http_response_code(404);
    echo "Nothing here.\n";
}
