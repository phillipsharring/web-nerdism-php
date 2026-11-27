<?php

declare(strict_types=1);

namespace App;

final readonly class Request
{
    /** @var array<string, string> header names in lower case */
    public array $headers;

    public function __construct(public string $method, public string $path, array $headers)
    {
        $this->headers = array_change_key_case($headers, CASE_LOWER);
    }

    public function header(string $name): string
    {
        return $this->headers[strtolower($name)] ?? '';
    }
}
