<?php

declare(strict_types=1);

// Knows how to build each kind of Mailer, and which one the settings ask for.
final class MailerFactory
{
    /** @param array<string, string> $settings */
    public function __construct(private array $settings)
    {
    }

    public function create(): Mailer
    {
        $kind = $this->settings['MAILER'] ?? '';

        return match ($kind) {
            'log' => new LogMailer(),
            'none' => new NullMailer(),
            'smtp' => new SmtpMailer($this->setting('SMTP_HOST'), $this->port()),
            default => throw new InvalidArgumentException("MAILER must be log, none or smtp, got '$kind'"),
        };
    }

    private function setting(string $name): string
    {
        $value = $this->settings[$name] ?? '';
        if ($value === '') {
            throw new InvalidArgumentException("$name is required for MAILER=smtp");
        }
        return $value;
    }

    private function port(): int
    {
        $port = filter_var($this->setting('SMTP_PORT'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 65535],
        ]);
        if ($port === false) {
            throw new InvalidArgumentException("SMTP_PORT must be 1 to 65535, got '{$this->settings['SMTP_PORT']}'");
        }
        return $port;
    }
}
