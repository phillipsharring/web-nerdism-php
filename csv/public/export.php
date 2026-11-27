<?php
declare(strict_types=1);

$shirts = [
    ['sku' => 'TS-01', 'name' => 'Plain tee', 'price_cents' => 1500],
    ['sku' => 'TS-02', 'name' => 'Tee, with pocket', 'price_cents' => 1800],
    ['sku' => 'TS-03', 'name' => 'The "Classic" tee', 'price_cents' => 2000],
    ['sku' => 'TS-04', 'name' => 'Café tee', 'price_cents' => 2200],
];

// A download: what it is, and the name to save it under.
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="shirts.csv"');

// fputcsv() adds the quotes a field needs: around commas, and doubled
// inside quotes. escape: '' turns off PHP's own, non-standard escaping.
$out = fopen('php://output', 'w');
fputcsv($out, array_keys($shirts[0]), escape: '');
foreach ($shirts as $shirt) {
    fputcsv($out, $shirt, escape: '');
}
fclose($out);
