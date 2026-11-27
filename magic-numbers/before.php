<?php

declare(strict_types=1);

// Orders as they come back from the database.
$orders = [
    ['id' => 101, 'status' => 2, 'subtotalCents' => 4200, 'paidOn' => '2026-11-20'],
    ['id' => 102, 'status' => 3, 'subtotalCents' => 6400, 'paidOn' => '2026-11-02'],
    ['id' => 103, 'status' => 1, 'subtotalCents' => 1800, 'paidOn' => null],
    ['id' => 104, 'status' => 9, 'subtotalCents' => 2500, 'paidOn' => '2026-11-25'],
];

foreach ($orders as $order) {
    if ($order['status'] === 1) {
        echo "#{$order['id']}: waiting for payment\n";
        continue;
    }

    $shippingCents = $order['subtotalCents'] >= 5000 ? 0 : 599;
    $taxCents = (int) round($order['subtotalCents'] * 0.0725);
    $returnBy = date('Y-m-d', strtotime($order['paidOn']) + 86400 * 30);
    $state = $order['status'] === 3 ? 'shipped' : 'paid';

    printf(
        "#%d: %s, shipping %.2f, tax %.2f, returns until %s\n",
        $order['id'],
        $state,
        $shippingCents / 100,
        $taxCents / 100,
        $returnBy,
    );
}
