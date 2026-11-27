<?php
declare(strict_types=1);

// Never trust input: a CSV someone hands you is input, every row of it.
$file = fopen($argv[1] ?? 'shirts.csv', 'r');
if ($file === false) {
    exit(1);
}

// Excel starts UTF-8 files with an invisible marker, the BOM. Drop it, or
// the first column's name won't match.
$header = fgetcsv($file, escape: '');
if (is_array($header)) {
    $header[0] = preg_replace('/^\x{FEFF}/u', '', (string) $header[0]);
}
if ($header !== ['sku', 'name', 'price_cents']) {
    fwrite(STDERR, "Expected the columns sku, name, price_cents.\n");
    exit(1);
}

$line = 1;
while (($fields = fgetcsv($file, escape: '')) !== false) {
    $line++;
    if (count($fields) !== count($header)) {
        echo "Line $line: wrong number of fields, skipped.\n";
        continue;
    }

    $row = array_combine($header, $fields);
    $price = filter_var($row['price_cents'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($price === false || !mb_check_encoding($row['name'], 'UTF-8')) {
        echo "Line $line: bad price or text, skipped.\n";
        continue;
    }

    echo "{$row['sku']}: {$row['name']}, $price cents\n";
}
fclose($file);
