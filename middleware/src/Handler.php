<?php

declare(strict_types=1);

namespace App;

// Anything that can turn a request into a response.
interface Handler
{
    public function handle(Request $request): Response;
}
