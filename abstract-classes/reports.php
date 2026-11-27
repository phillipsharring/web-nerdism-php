<?php
declare(strict_types=1);

require __DIR__ . '/SalesReport.php';
require __DIR__ . '/SignupReport.php';

// Different reports, one way to print them.
foreach ([new SalesReport(), new SignupReport()] as $report) {
    echo $report->render(), "\n";
}
