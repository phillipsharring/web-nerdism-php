<?php
declare(strict_types=1);

namespace App;

// Checks submitted data one field at a time. Each method returns the clean
// value, or null if the field failed, and records an error under the
// field's name. Never trust input: everything in $data is assumed hostile
// until a method here says otherwise.
final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /** @param array<string, mixed> $data usually $_POST */
    public function __construct(private array $data)
    {
    }

    public function text(string $field, string $label, int $max, bool $required = true): ?string
    {
        $value = $this->data[$field] ?? '';
        if (!is_string($value) || !mb_check_encoding($value, 'UTF-8')) {
            return $this->fail($field, "$label must be text.");
        }
        $value = trim($value);
        if ($value === '') {
            return $required ? $this->fail($field, "$label is required.") : '';
        }
        if (mb_strlen($value) > $max) {
            return $this->fail($field, "$label can be at most $max characters.");
        }

        return $value;
    }

    public function email(string $field, string $label): ?string
    {
        $value = $this->text($field, $label, 254);
        if ($value === null) {
            return null;
        }
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            return $this->fail($field, "$label must be an email address.");
        }

        return $value;
    }

    public function int(string $field, string $label, int $min, int $max): ?int
    {
        $value = $this->text($field, $label, 20);
        if ($value === null) {
            return null;
        }
        $number = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $max]]);
        if ($number === false) {
            return $this->fail($field, "$label must be a whole number from $min to $max.");
        }

        return $number;
    }

    /** @param list<string> $choices */
    public function oneOf(string $field, string $label, array $choices): ?string
    {
        $value = $this->text($field, $label, 100);
        if ($value === null) {
            return null;
        }
        if (!in_array($value, $choices, true)) {
            return $this->fail($field, "$label must be one of the choices.");
        }

        return $value;
    }

    public function passes(): bool
    {
        return $this->errors === [];
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }

    public function error(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    private function fail(string $field, string $message): null
    {
        // Keep the first problem with each field; one at a time is plenty.
        $this->errors[$field] ??= $message;

        return null;
    }
}
