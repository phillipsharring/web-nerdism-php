<?php
declare(strict_types=1);

/**
 * Talks to an API over HTTPS (or HTTP, here, locally). It's given the address
 * and the token; it never knows where they came from, and never prints the
 * token anywhere.
 */
final class ApiClient
{
    public function __construct(
        private string $baseUrl,
        #[\SensitiveParameter] private string $token,   // kept out of stack traces
        private int $timeoutSeconds = 5,
        private int $maxAttempts = 3,
    ) {
        // Tokens only travel over HTTPS, except to this machine's own containers.
        if (!preg_match('#^(https://|http://(localhost|api)[:/])#', $baseUrl . '/')) {
            throw new InvalidArgumentException("Refusing to send a token to $baseUrl: use https://.");
        }
    }

    /** GETs a path and returns the decoded JSON object. */
    public function get(string $path): array
    {
        for ($attempt = 1; ; $attempt++) {
            [$status, $headers, $body] = $this->send($path);

            // Busy or rate limited: wait as long as they ask (within reason), and retry.
            if (in_array($status, [429, 503], true) && $attempt < $this->maxAttempts) {
                sleep(min(max((int) ($headers['retry-after'] ?? 1), 1), 10));
                continue;
            }
            if ($status < 200 || $status > 299) {
                throw new ApiError("The API answered $status for $path.", $status);
            }

            // The answer is input too. Never trust it: check it's JSON, and an object.
            try {
                $data = json_decode($body, true, 32, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                throw new ApiError("The API's answer for $path wasn't JSON.", $status);
            }
            if (!is_array($data)) {
                throw new ApiError("The API's answer for $path wasn't a JSON object.", $status);
            }

            return $data;
        }
    }

    /** @return array{0: int, 1: array<string, string>, 2: string} status, headers, body */
    private function send(string $path): array
    {
        $headers = [];
        $curl = curl_init($this->baseUrl . $path);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $this->token, 'Accept: application/json'],
            CURLOPT_CONNECTTIMEOUT => $this->timeoutSeconds,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_FOLLOWLOCATION => false,                 // a redirect could carry the token elsewhere
            CURLOPT_HEADERFUNCTION => function ($curl, string $line) use (&$headers): int {
                if (str_contains($line, ':')) {
                    [$name, $value] = explode(':', $line, 2);
                    $headers[strtolower(trim($name))] = trim($value);
                }
                return strlen($line);
            },
        ]);
        $body = curl_exec($curl);
        if ($body === false) {
            // curl's own message, which names the problem but not the token.
            throw new ApiError("Couldn't reach the API for $path: " . curl_error($curl));
        }

        return [curl_getinfo($curl, CURLINFO_RESPONSE_CODE), $headers, $body];
    }
}
