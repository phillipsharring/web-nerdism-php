<?php
declare(strict_types=1);

// The same download done the easy way: the whole file into a string, then out.
$path = dirname(__DIR__) . '/files/big.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="everyone.csv"');

echo file_get_contents($path);
