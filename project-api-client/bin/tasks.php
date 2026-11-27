<?php
declare(strict_types=1);

// Lists the tasks from the API. The address and the token come from the
// environment, never from this file:
//   API_URL=http://api:8000 API_TOKEN=... php bin/tasks.php [PATH]

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require dirname(__DIR__) . '/src/ApiError.php';
require dirname(__DIR__) . '/src/ApiClient.php';

$url = getenv('API_URL');
$token = getenv('API_TOKEN');
if (!$url || !$token) {
    fwrite(STDERR, "Set API_URL and API_TOKEN in the environment (see .env.example).\n");
    exit(2);
}

$client = new ApiClient(rtrim($url, '/'), $token);
try {
    $data = $client->get($argv[1] ?? '/tasks');
} catch (ApiError $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

if (($argv[1] ?? '/tasks') !== '/tasks') {
    echo json_encode($data), "\n";
    exit(0);
}

// Check the shape before using it: an API can change, or be wrong.
foreach ($data['tasks'] ?? [] as $task) {
    if (!is_int($task['id'] ?? null) || !is_string($task['title'] ?? null) || !is_bool($task['done'] ?? null)) {
        fwrite(STDERR, "Skipping a task that isn't shaped like one.\n");
        continue;
    }
    printf("[%s] %d %s\n", $task['done'] ? 'x' : ' ', $task['id'], $task['title']);
}
