<?php

declare(strict_types=1);

namespace App;

// Runs the middleware in order, then the handler at the end.
final class Pipeline implements Handler
{
    /** @param Middleware[] $middleware */
    public function __construct(private array $middleware, private Handler $last)
    {
    }

    public function handle(Request $request): Response
    {
        if ($this->middleware === []) {
            return $this->last->handle($request);
        }

        [$first, $rest] = [$this->middleware[0], array_slice($this->middleware, 1)];
        return $first->process($request, new self($rest, $this->last));
    }
}
