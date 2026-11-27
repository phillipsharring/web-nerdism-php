<?php
declare(strict_types=1);

// Half a class, on purpose. It knows how every report is laid out,
// but not what goes in one. You can't make a Report, only something built on it.
abstract class Report
{
    // The same for every report. final: no subclass may change it.
    final public function render(): string
    {
        $title = $this->title();
        $lines = [$title, str_repeat('=', strlen($title))];
        foreach ($this->rows() as $label => $value) {
            $lines[] = sprintf('%-10s %10s', $label, $value);
        }
        return implode("\n", $lines) . "\n";
    }

    // A helper every report can use.
    protected function dollars(int $cents): string
    {
        return sprintf('$%s.%02d', number_format(intdiv($cents, 100)), $cents % 100);
    }

    // No body: each report must write its own.
    abstract protected function title(): string;

    abstract protected function rows(): array;
}
