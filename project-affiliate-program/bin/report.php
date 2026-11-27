<?php
declare(strict_types=1);

// Clicks, orders and commissions for each affiliate.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require dirname(__DIR__) . '/src/db.php';

$rows = connect()->query(
    "SELECT a.code,
            (SELECT COUNT(*) FROM clicks WHERE affiliate_id = a.id) AS clicks,
            (SELECT COUNT(*) FROM orders WHERE affiliate_id = a.id) AS orders,
            (SELECT COALESCE(SUM(amount_cents), 0) FROM commissions WHERE affiliate_id = a.id AND status = 'pending') AS pending_cents
       FROM affiliates a ORDER BY a.code"
)->fetchAll();

printf("%-12s %7s %7s %10s\n", 'Code', 'Clicks', 'Orders', 'Pending');
foreach ($rows as $r) {
    $c = (int) $r['pending_cents'];
    printf("%-12s %7d %7d %10s\n", $r['code'], $r['clicks'], $r['orders'], '$' . intdiv($c, 100) . '.' . str_pad((string) ($c % 100), 2, '0', STR_PAD_LEFT));
}
