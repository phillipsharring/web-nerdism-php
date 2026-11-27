<?php
declare(strict_types=1);

const REGIONS = ['north', 'south', 'east', 'west'];

/**
 * Stands in for something slow: a big query, a call to another site, a
 * report that adds up a year of orders. It takes a full second every time.
 *
 * @return array{region: string, orders: int, revenue_cents: int, made_at: string}
 */
function slowReport(string $region): array
{
    sleep(1);
    $seed = crc32($region);

    return [
        'region' => $region,
        'orders' => 100 + $seed % 900,
        'revenue_cents' => 250000 + $seed % 750000,
        'made_at' => gmdate('H:i:s'),
    ];
}
