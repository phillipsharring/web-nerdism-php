<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Handler;
use App\Middleware;
use App\Request;
use App\Response;

// Works before: a request without a good token stops here, and never reaches the handler.
final class RequireToken implements Middleware
{
    /** @param string[] $tokens */
    public function __construct(private array $tokens)
    {
    }

    public function process(Request $request, Handler $next): Response
    {
        // Never trust input: the header is whatever the client sent.
        $header = $request->header('Authorization');
        $token = str_starts_with($header, 'Bearer ') ? substr($header, 7) : '';
        if (!in_array($token, $this->tokens, true)) {
            return new Response(401, ['error' => 'Log in first.'], ['WWW-Authenticate' => 'Bearer']);
        }

        return $next->handle($request);
    }
}
