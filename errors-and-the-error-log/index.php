<?php
declare(strict_types=1);

// A page that goes wrong on purpose: ?problem=warning or ?problem=exception.
// Never trust input: only the two known values count, so nothing odd reaches the log.
$problem = $_GET['problem'] ?? '';
if (!in_array($problem, ['warning', 'exception'], true)) {
    $problem = 'none';
}

// Your own messages go to the same log as PHP's.
error_log('Order page viewed, problem: ' . $problem);

header('Content-Type: text/plain');

if ($problem === 'exception') {
    // Nothing catches this, so it's fatal: the script stops here.
    throw new RuntimeException('The payment service did not answer');
}

$order = ['id' => 1001];
if ($problem === 'warning') {
    // No 'email' key: a warning, and the script carries on with null.
    echo 'Receipt sent to ', $order['email'], "\n";
}

echo 'Order ', $order['id'], " is done.\n";
