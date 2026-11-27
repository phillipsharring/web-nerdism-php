<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Handler;
use App\Middleware;
use App\Request;
use App\Response;
use Throwable;

// Outermost: whatever goes wrong inside, the client gets a plain 500, and the details go to the log.
final class CatchErrors implements Middleware
{
    public function process(Request $request, Handler $next): Response
    {
        try {
            return $next->handle($request);
        } catch (Throwable $e) {
            error_log("{$request->method} {$request->path}: {$e->getMessage()}");
            return new Response(500, ['error' => 'Something went wrong.']);
        }
    }
}
