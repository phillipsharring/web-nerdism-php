<?php
declare(strict_types=1);

// Counts failed sign-ins in the last 15 minutes, per address and per IP.
// Ten wrong passwords for one account, or fifty from one IP across any
// accounts, and sign-in is closed for a while, right password or not.
final class LoginLimiter
{
    private const MINUTES = 15;
    private const PER_EMAIL = 10;
    private const PER_IP = 50;

    public function __construct(private PDO $db)
    {
    }

    public function tooMany(string $email, string $ip): bool
    {
        $window = 'failed_at > NOW() - INTERVAL ' . self::MINUTES . ' MINUTE';

        $byEmail = $this->db->prepare("SELECT COUNT(*) FROM login_failures WHERE email = ? AND $window");
        $byEmail->execute([$email]);
        $byIp = $this->db->prepare("SELECT COUNT(*) FROM login_failures WHERE ip = ? AND $window");
        $byIp->execute([$ip]);

        return $byEmail->fetchColumn() >= self::PER_EMAIL || $byIp->fetchColumn() >= self::PER_IP;
    }

    public function fail(string $email, string $ip): void
    {
        $this->db->prepare('INSERT INTO login_failures (email, ip) VALUES (?, ?)')->execute([$email, $ip]);
    }
}
