<?php
declare(strict_types=1);

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;

require dirname(__DIR__) . '/vendor/autoload.php';

// One id per request. It goes in every log line and back to the browser, so
// "it broke" can be matched to exactly what the log says happened.
$requestId = bin2hex(random_bytes(8));
header('X-Request-Id: ' . $requestId);

$logger = new Logger('app');
$logger->pushHandler(new StreamHandler(dirname(__DIR__) . '/logs/app.log', Level::Debug));
$logger->pushProcessor(function (LogRecord $record) use ($requestId): LogRecord {
    $record->extra['request_id'] = $requestId;

    return $record;
});

// Anything thrown and not caught: log the details, show the visitor
// only an apology and the id.
set_exception_handler(function (Throwable $e) use ($logger, $requestId): void {
    $logger->error('Uncaught exception', ['exception' => $e]);
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Something went wrong. If you report it, mention this: $requestId\n";
});
