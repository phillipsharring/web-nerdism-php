<?php
declare(strict_types=1);

namespace App;

// Allows $max hits per key in any $seconds window. Each key's recent hits
// are a list of times in a small file, locked while it's read and written,
// so two requests at once can't both squeeze in.
final class FileRateLimiter
{
    public function __construct(
        private string $dir,
        private int $max,
        private int $seconds,
    ) {
    }

    public function attempt(string $key): bool
    {
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0700, true);
        }
        $file = $this->dir . '/' . hash('sha256', $key) . '.json';
        $handle = fopen($file, 'c+');
        flock($handle, LOCK_EX);

        $now = time();
        $hits = json_decode(stream_get_contents($handle) ?: '[]', true) ?: [];
        $hits = array_values(array_filter($hits, fn (int $t): bool => $t > $now - $this->seconds));

        $allowed = count($hits) < $this->max;
        if ($allowed) {
            $hits[] = $now;
        }

        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, json_encode($hits));
        flock($handle, LOCK_UN);
        fclose($handle);

        return $allowed;
    }
}
