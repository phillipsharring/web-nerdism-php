<?php

declare(strict_types=1);

use App\Middleware\AddRequestId;
use App\Middleware\CatchErrors;
use App\Middleware\RequireToken;
use App\NotesHandler;
use App\Pipeline;
use App\Request;

require dirname(__DIR__) . '/vendor/autoload.php';

// The order is the design: errors outermost, then the request id, then the login check.
$app = new Pipeline(
    [new CatchErrors(), new AddRequestId(), new RequireToken(['alice-token'])],
    new NotesHandler(),
);

$request = new Request(
    $_SERVER['REQUEST_METHOD'],
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
    getallheaders(),
);
$response = $app->handle($request);

http_response_code($response->status);
header('Content-Type: application/json; charset=utf-8');
foreach ($response->headers as $name => $value) {
    header("$name: $value");
}
echo json_encode($response->body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), "\n";
