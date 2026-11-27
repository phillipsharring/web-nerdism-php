<?php
declare(strict_types=1);

// PHP to JSON. Money is counted in cents, an int, never a float.
$order = ['id' => 42, 'size' => 'large', 'gift' => true, 'note' => null, 'price_cents' => 1999];
echo json_encode($order, JSON_PRETTY_PRINT), "\n";

// A list becomes a JSON array, and keys make it an object. An empty array
// could be either, and comes out as [].
echo json_encode(['red', 'blue']), "\n";
echo json_encode(['size' => 'large']), "\n";
echo json_encode([]), ' ', json_encode((object) []), "\n";

// JSON to PHP. true asks for arrays; without it, you get objects.
var_dump(json_decode('{"size":"large","qty":2}', true, 512, JSON_THROW_ON_ERROR));

// Text that isn't JSON, or isn't UTF-8, is an error. JSON_THROW_ON_ERROR
// makes it an exception, so it can't slip by.
try {
    json_decode('{size: large}', true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    echo 'Not JSON: ', $e->getMessage(), "\n";
}
try {
    json_encode("caf\xE9", JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    echo 'Not UTF-8: ', $e->getMessage(), "\n";
}

// Accented letters come out as \u codes, unless you ask for them as they are.
echo json_encode('café'), ' ', json_encode('café', JSON_UNESCAPED_UNICODE), "\n";
