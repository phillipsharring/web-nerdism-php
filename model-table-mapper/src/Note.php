<?php

declare(strict_types=1);

namespace App;

use DateTimeImmutable;
use InvalidArgumentException;

// The model: one note, and its rules. It has never heard of a database.
final readonly class Note
{
    public function __construct(
        public ?int $id,
        public string $body,
        public DateTimeImmutable $createdAt,
    ) {
        $length = mb_strlen(trim($body));
        if ($length < 1 || $length > 500) {
            throw new InvalidArgumentException("A note is 1 to 500 characters, got $length");
        }
    }

    public function withBody(string $body): self
    {
        return new self($this->id, $body, $this->createdAt);
    }
}
