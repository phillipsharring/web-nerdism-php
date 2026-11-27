<?php
declare(strict_types=1);

/** Who sent a customer, and what they're owed for it. */
final class Affiliates
{
    public const COOKIE = 'ref';
    public const COOKIE_DAYS = 30;

    public function __construct(private PDO $db)
    {
    }

    /**
     * The active affiliate with this code, or null. Never trust input: the
     * code comes from a link anyone can edit, or a cookie anyone can set, so
     * it's checked for shape and then looked up, every time.
     *
     * @return array{id: int, code: string, email: string, commission_bps: int}|null
     */
    public function find(mixed $code): ?array
    {
        if (!is_string($code) || !preg_match('/^[A-Z0-9]{3,20}$/', $code)) {
            return null;
        }
        $stmt = $this->db->prepare('SELECT id, code, email, commission_bps FROM affiliates WHERE code = ? AND active = 1');
        $stmt->execute([$code]);
        $row = $stmt->fetch();

        return $row ? ['id' => (int) $row['id'], 'code' => $row['code'], 'email' => $row['email'], 'commission_bps' => (int) $row['commission_bps']] : null;
    }

    public function recordClick(int $affiliateId): void
    {
        $this->db->prepare('INSERT INTO clicks (affiliate_id) VALUES (?)')->execute([$affiliateId]);
    }

    /**
     * The cut of a total, in whole cents, rounded down: 10% of $49.99 is
     * $4.99, never $4.999. Integer math only.
     */
    public function commission(int $totalCents, int $bps): int
    {
        return intdiv($totalCents * $bps, 10000);
    }

    /**
     * Records an order, and the commission if an affiliate sent it. Returns
     * the commission in cents, 0 if there isn't one.
     */
    public function recordOrder(string $email, int $totalCents, ?array $affiliate): int
    {
        // Affiliates buying through their own link don't earn on it.
        if ($affiliate !== null && strcasecmp($affiliate['email'], $email) === 0) {
            $affiliate = null;
        }

        $this->db->beginTransaction();
        $this->db->prepare('INSERT INTO orders (email, total_cents, affiliate_id) VALUES (?, ?, ?)')
            ->execute([$email, $totalCents, $affiliate['id'] ?? null]);
        $orderId = (int) $this->db->lastInsertId();

        $amount = 0;
        if ($affiliate !== null) {
            $amount = $this->commission($totalCents, $affiliate['commission_bps']);
            $this->db->prepare('INSERT INTO commissions (affiliate_id, order_id, amount_cents) VALUES (?, ?, ?)')
                ->execute([$affiliate['id'], $orderId, $amount]);
        }
        $this->db->commit();

        return $amount;
    }
}
