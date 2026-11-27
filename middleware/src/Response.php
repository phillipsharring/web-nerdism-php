<?php

declare(strict_types=1);

namespace App;

final readonly class Response
{
    /** @param array<string, string> $headers */
    public function __construct(public int $status, public array $body, public array $headers = [])
    {
    }

    public function withHeader(string $name, string $value): self
    {
        return new self($this->status, $this->body, [...$this->headers, $name => $value]);
    }
}
