<?php
declare(strict_types=1);

require_once __DIR__ . '/Report.php';

class SalesReport extends Report
{
    protected function title(): string
    {
        return 'Sales';
    }

    protected function rows(): array
    {
        return [
            'September' => $this->dollars(120_000),
            'October' => $this->dollars(145_050),
            'November' => $this->dollars(161_099),
        ];
    }
}
