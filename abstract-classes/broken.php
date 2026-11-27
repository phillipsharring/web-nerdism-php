<?php
declare(strict_types=1);

require __DIR__ . '/Report.php';

// Forgot rows(). PHP won't let this class exist.
class RefundReport extends Report
{
    protected function title(): string
    {
        return 'Refunds';
    }
}
