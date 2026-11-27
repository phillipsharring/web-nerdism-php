<?php

declare(strict_types=1);

// A singleton: one instance, made on first use, reachable from anywhere.
final class Mailer
{
    private static ?Mailer $instance = null;

    /** @var string[] */
    private array $sent = [];

    // Private, so nobody else can say "new Mailer()".
    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function send(string $to, string $subject): void
    {
        // A real mailer would send it. This one writes it down.
        $this->sent[] = "$to: $subject";
    }

    /** @return string[] */
    public function sent(): array
    {
        return $this->sent;
    }
}
