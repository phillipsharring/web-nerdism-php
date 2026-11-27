<?php
declare(strict_types=1);

/** Something went wrong talking to the API. The message never includes the token. */
final class ApiError extends RuntimeException
{
    public function __construct(string $message, public readonly ?int $status = null)
    {
        parent::__construct($message);
    }
}
