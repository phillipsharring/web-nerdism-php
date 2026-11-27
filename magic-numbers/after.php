<?php

declare(strict_types=1);

namespace Shop;

use DateTimeImmutable;

require __DIR__ . '/rules.php';

// Orders as they come back from the database.
$orders = [
    ['id' => 101, 'status' => 2, 'subtotalCents' => 4200, 'paidOn' => '2026-11-20'],
    ['id' => 102, 'status' => 3, 'subtotalCents' => 6400, 'paidOn' => '2026-11-02'],
    ['id' => 103, 'status' => 1, 'subtotalCents' => 1800, 'paidOn' => null],
    ['id' => 104, 'status' => 9, 'subtotalCents' => 2500, 'paidOn' => '2026-11-25'],
];

foreach ($orders as $order) {
    // The database is input too: a status we don't know is a status we don't guess at.
    $status = OrderStatus::tryFrom($order['status']);
    if ($status === null) {
        echo "#{$order['id']}: unknown status {$order['status']}, skipped\n";
        continue;
    }

    if ($status === OrderStatus::Pending) {
        echo "#{$order['id']}: waiting for payment\n";
        continue;
    }

    $shippingCents = $order['subtotalCents'] >= FREE_SHIPPING_FROM_CENTS ? 0 : SHIPPING_CENTS;
    $taxCents = (int) round($order['subtotalCents'] * SALES_TAX_RATE);
    $returnBy = (new DateTimeImmutable($order['paidOn']))
        ->modify('+' . RETURN_WINDOW_DAYS . ' days')
        ->format('Y-m-d');

    printf(
        "#%d: %s, shipping %.2f, tax %.2f, returns until %s\n",
        $order['id'],
        strtolower($status->name),
        $shippingCents / 100,
        $taxCents / 100,
        $returnBy,
    );
}
