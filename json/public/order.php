<?php
declare(strict_types=1);

header('Content-Type: application/json');

function reply(int $status, array $body): never
{
    http_response_code($status);
    echo json_encode($body, JSON_THROW_ON_ERROR);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    reply(405, ['error' => 'POST an order, as JSON.']);
}

// A JSON body isn't in $_POST. Read it as it came, and decode it.
try {
    $order = json_decode(file_get_contents('php://input'), true, 16, JSON_THROW_ON_ERROR);
} catch (JsonException) {
    reply(400, ['error' => 'That is not JSON.']);
}

// Never trust input: valid JSON can still be the wrong shape, or the wrong types.
$sizes = ['small', 'medium', 'large'];
if (
    !is_array($order)
    || !in_array($order['size'] ?? null, $sizes, true)
    || !is_int($order['qty'] ?? null)
    || $order['qty'] < 1
    || $order['qty'] > 10
) {
    reply(422, ['error' => 'Send a size (small, medium or large) and a qty from 1 to 10.']);
}

reply(201, ['ordered' => ['size' => $order['size'], 'qty' => $order['qty']]]);
