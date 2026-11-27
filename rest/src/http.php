<?php

declare(strict_types=1);

function send(int $status, ?array $body = null, array $headers = []): never
{
    http_response_code($status);
    foreach ($headers as $name => $value) {
        header("$name: $value");
    }
    if ($body !== null) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), "\n";
    }
    exit;
}

// Never trust input: the body has to be JSON, an object, with a body of 1 to 500 characters.
function noteFromRequest(): string
{
    if (!str_starts_with($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
        send(415, ['error' => 'Send JSON, with Content-Type: application/json.']);
    }
    try {
        $data = json_decode(file_get_contents('php://input'), true, 8, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        send(400, ['error' => "That isn't valid JSON."]);
    }
    $body = is_array($data) && is_string($data['body'] ?? null) ? trim($data['body']) : '';
    if ($body === '' || mb_strlen($body) > 500) {
        send(422, ['error' => 'A note needs a "body" of 1 to 500 characters.']);
    }
    return $body;
}
