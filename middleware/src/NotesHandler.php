<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

// The handler at the end of the pipe. It only has to do its own job.
final class NotesHandler implements Handler
{
    public function handle(Request $request): Response
    {
        return match ($request->path) {
            '/notes' => new Response(200, ['notes' => ['Order more oat milk', 'Fix the café sign']]),
            '/boom' => throw new RuntimeException('the database is on fire'),
            default => new Response(404, ['error' => 'Not found.']),
        };
    }
}
