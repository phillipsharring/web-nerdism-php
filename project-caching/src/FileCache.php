<?php
declare(strict_types=1);

/**
 * Keeps computed values in files for a while, so the work is done once and
 * read many times. Values are stored as JSON: plain data, nothing that runs.
 */
final class FileCache
{
    public function __construct(private string $dir)
    {
    }

    /**
     * The cached value for $key, or, if there isn't a fresh one, the result of
     * $compute(), which is then kept for $seconds. $hit says which it was.
     */
    public function remember(string $key, int $seconds, callable $compute, ?bool &$hit = null): mixed
    {
        $path = $this->path($key);
        $stored = is_file($path) ? json_decode((string) file_get_contents($path), true) : null;
        if (is_array($stored) && ($stored['expires'] ?? 0) > time()) {
            $hit = true;
            return $stored['value'];
        }

        $hit = false;
        $value = $compute();

        // Write to a temporary file, then rename it into place. A rename is all
        // or nothing, so no request ever reads a half-written file.
        $temp = tempnam($this->dir, 'tmp');
        file_put_contents($temp, json_encode(['expires' => time() + $seconds, 'value' => $value], JSON_THROW_ON_ERROR));
        rename($temp, $path);

        return $value;
    }

    public function forget(string $key): void
    {
        @unlink($this->path($key));
    }

    /** Throws away everything. */
    public function clear(): int
    {
        $files = glob($this->dir . '/*.json');
        array_map('unlink', $files);
        return count($files);
    }

    private function path(string $key): string
    {
        // A hash makes any key a safe file name.
        return $this->dir . '/' . hash('sha256', $key) . '.json';
    }
}
