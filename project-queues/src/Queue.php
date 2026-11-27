<?php
declare(strict_types=1);

namespace App;

use PDO;

/** A queue of jobs in a database table. */
final class Queue
{
    public const MAX_ATTEMPTS = 3;
    private const STUCK_AFTER_MINUTES = 10;

    public function __construct(private PDO $db)
    {
    }

    /** Adds a job. Returns its id. */
    public function push(string $type, array $payload): int
    {
        $this->db->prepare('INSERT INTO jobs (type, payload) VALUES (?, ?)')
            ->execute([$type, json_encode($payload, JSON_THROW_ON_ERROR)]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Takes the next job that's ready, so no other worker can take it too.
     * Also takes back jobs a crashed worker left "running" for too long.
     *
     * @return array{id: int, type: string, payload: array, attempts: int}|null
     */
    public function reserve(): ?array
    {
        $this->db->beginTransaction();
        // FOR UPDATE locks the row; SKIP LOCKED makes other workers pass over
        // it instead of waiting, so each job goes to exactly one of them.
        $job = $this->db->query(
            "SELECT id, type, payload, attempts FROM jobs
              WHERE (status = 'queued' AND available_at <= NOW())
                 OR (status = 'running' AND reserved_at < NOW() - INTERVAL " . self::STUCK_AFTER_MINUTES . " MINUTE)
              ORDER BY id LIMIT 1
              FOR UPDATE SKIP LOCKED"
        )->fetch();

        if ($job === false) {
            $this->db->commit();
            return null;
        }

        $this->db->prepare("UPDATE jobs SET status = 'running', reserved_at = NOW(), attempts = attempts + 1 WHERE id = ?")
            ->execute([$job['id']]);
        $this->db->commit();

        return [
            'id' => (int) $job['id'],
            'type' => $job['type'],
            'payload' => json_decode($job['payload'], true, 512, JSON_THROW_ON_ERROR),
            'attempts' => (int) $job['attempts'] + 1,
        ];
    }

    public function complete(int $id): void
    {
        $this->db->prepare("UPDATE jobs SET status = 'done', last_error = NULL WHERE id = ?")->execute([$id]);
    }

    /** Puts a failed job back to try later, or gives up after MAX_ATTEMPTS. */
    public function release(int $id, int $attempts, string $error, int $delaySeconds): string
    {
        $status = $attempts >= self::MAX_ATTEMPTS ? 'failed' : 'queued';
        $this->db->prepare(
            'UPDATE jobs SET status = ?, last_error = ?, available_at = NOW() + INTERVAL ? SECOND WHERE id = ?'
        )->execute([$status, mb_substr($error, 0, 2000), $delaySeconds, $id]);

        return $status;
    }
}
