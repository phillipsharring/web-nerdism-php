<?php
declare(strict_types=1);

// The last line of defense: any exception nobody caught ends up here.
set_exception_handler(function (Throwable $e): void {
    error_log((string) $e); // The whole story, with the stack trace, for you.

    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Something went wrong. It's been logged.\n"; // The short story, for them.
});
