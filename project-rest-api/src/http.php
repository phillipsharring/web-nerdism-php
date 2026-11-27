<?php
declare(strict_types=1);

/** Sends a JSON answer with a status code, and stops. */
function respond(int $status, ?array $body = null, array $headers = []): never
{
    http_response_code($status);
    foreach ($headers as $name => $value) {
        header("$name: $value");
    }
    if ($body !== null) {
        header('Content-Type: application/json');
        echo json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), "\n";
    }
    exit;
}

/** An error, in the same shape every time. */
function fail(int $status, string $message, array $headers = []): never
{
    respond($status, ['error' => $message], $headers);
}

/**
 * Who is calling: the token in "Authorization: Bearer ...". No token, or one
 * we don't know, is a 401.
 *
 * @return array{user_id: int, can_write: bool}
 */
function authenticate(PDO $db): array
{
    // Never trust input. The header is whatever the caller sent.
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!preg_match('/^Bearer ([A-Za-z0-9_]{10,100})$/', $header, $m)) {
        fail(401, 'Send your token: Authorization: Bearer <token>.', ['WWW-Authenticate' => 'Bearer realm="tasks"']);
    }

    // Look the token up by its hash: the database never holds the token itself.
    $stmt = $db->prepare('SELECT id, user_id, can_write FROM api_tokens WHERE token_hash = ?');
    $stmt->execute([hash('sha256', $m[1])]);
    $token = $stmt->fetch();
    if ($token === false) {
        fail(401, 'That token isn\'t valid.', ['WWW-Authenticate' => 'Bearer realm="tasks", error="invalid_token"']);
    }
    $db->prepare('UPDATE api_tokens SET last_used_at = NOW() WHERE id = ?')->execute([$token['id']]);

    return ['user_id' => (int) $token['user_id'], 'can_write' => (bool) $token['can_write']];
}

/** The request body as JSON. Anything else is the caller's mistake: 415 or 400. */
function readJson(): array
{
    $type = strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '')[0]));
    if ($type !== 'application/json') {
        fail(415, 'Send JSON, with Content-Type: application/json.');
    }
    try {
        $data = json_decode((string) file_get_contents('php://input'), true, 16, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        fail(400, 'That body isn\'t valid JSON.');
    }
    if (!is_array($data) || array_is_list($data) && $data !== []) {
        fail(400, 'Send a JSON object.');
    }

    return $data;
}
