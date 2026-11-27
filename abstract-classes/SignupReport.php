<?php
declare(strict_types=1);

require_once __DIR__ . '/Report.php';

class SignupReport extends Report
{
    protected function title(): string
    {
        return 'Signups';
    }

    protected function rows(): array
    {
        return ['This week' => 42, 'Last week' => 37];
    }
}
