<?php
if ($_SERVER['REQUEST_URI'] === '/teapot') {
    http_response_code(418);
}
header('Content-Type: text/plain');

echo $_SERVER['REQUEST_METHOD'], ' ', $_SERVER['REQUEST_URI'], "\n\n";
foreach (getallheaders() as $name => $value) {
    echo "$name: $value\n";
}
echo "\n", file_get_contents('php://input'), "\n";
