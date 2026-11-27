<?php

declare(strict_types=1);

namespace App;

// One layer: do something before, call $next (or don't), do something after.
interface Middleware
{
    public function process(Request $request, Handler $next): Response;
}
