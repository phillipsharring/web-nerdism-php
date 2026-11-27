<?php
declare(strict_types=1);

// Writes files/big.csv, a million made-up rows (about 40 MB), for the downloads to send.
$path = __DIR__ . '/files/big.csv';
$out = fopen($path, 'w');
fwrite($out, "id,email,joined\n");
for ($i = 1; $i <= 1_000_000; $i++) {
    fwrite($out, sprintf("%d,user%d@example.com,2026-11-%02d\n", $i, $i, $i % 28 + 1));
}
fclose($out);

echo 'Wrote ', filesize($path), " bytes to files/big.csv.\n";
