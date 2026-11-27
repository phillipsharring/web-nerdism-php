<?php
declare(strict_types=1);

/**
 * Makes a value safe to open in a spreadsheet. A cell that starts with =, +,
 * -, @, a tab or a carriage return is read as a formula by Excel, LibreOffice
 * and Google Sheets. A leading apostrophe tells them it's text.
 * Never trust input: these values came from a form, and a spreadsheet is
 * another place they can run.
 */
function spreadsheetSafe(string $value): string
{
    // A plain number, like -5.00, is only ever a number: leave it alone.
    if (preg_match('/^-?\d+(\.\d+)?$/', $value)) {
        return $value;
    }

    return preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
}

/**
 * Writes one row. The escape argument is given as "" on purpose: PHP 8.4
 * warns when it's left out, and "" means quotes are escaped by doubling them,
 * the way every spreadsheet expects.
 *
 * @param resource $out
 * @param list<string|int> $row
 */
function writeRow($out, array $row): void
{
    $row = array_map(fn ($v) => is_string($v) ? spreadsheetSafe($v) : $v, $row);
    fputcsv($out, $row, ',', '"', '', "\r\n");
}
