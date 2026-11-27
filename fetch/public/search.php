<?php
declare(strict_types=1);

$colors = [
    'Black', 'Blue', 'Brown', 'Gray', 'Green', 'Navy',
    'Orange', 'Pink', 'Purple', 'Red', 'White', 'Yellow',
];

header('Content-Type: application/json');

// Never trust input. fetch() is just another client: anyone can call this
// URL, with anything in it.
$q = $_GET['q'] ?? '';
if (!is_string($q) || mb_strlen($q) > 50) {
    http_response_code(400);
    echo json_encode(['error' => 'Search for 50 characters or fewer.']);
    exit;
}

$q = trim($q);
$matches = [];
foreach ($colors as $color) {
    if ($q !== '' && stripos($color, $q) === 0) {
        $matches[] = $color;
    }
}

echo json_encode(['query' => $q, 'matches' => $matches], JSON_THROW_ON_ERROR);
