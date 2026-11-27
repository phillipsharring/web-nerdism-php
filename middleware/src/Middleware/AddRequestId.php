<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Handler;
use App\Middleware;
use App\Request;
use App\Response;

// Works after: lets the request through, then stamps the response on its way out.
final class AddRequestId implements Middleware
{
    public function process(Request $request, Handler $next): Response
    {
        return $next->handle($request)->withHeader('X-Request-Id', bin2hex(random_bytes(8)));
    }
}
