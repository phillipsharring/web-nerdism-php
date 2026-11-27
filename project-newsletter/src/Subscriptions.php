<?php
declare(strict_types=1);

namespace App;

use PDO;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Subscribing, confirming and unsubscribing. The rule behind all of it: the
 * newsletter goes only to people who asked for it, proved the address is
 * theirs, and can stop it with one click.
 */
final class Subscriptions
{
    private const PER_IP_PER_HOUR = 5;
    private const CONFIRM_DAYS = 7;

    public function __construct(
        private PDO $db,
        private MailerInterface $mailer,
        private string $siteUrl,
        private string $secret,
    ) {
    }

    /**
     * Someone typed an address into the form. Sends at most one confirmation
     * a day to any address, and only a few an hour from any one visitor.
     * Returns false if this visitor has hit the limit.
     */
    public function subscribe(string $email, string $ip): bool
    {
        if ($this->count('SELECT COUNT(*) FROM signup_mail WHERE ip = ? AND sent_at > NOW() - INTERVAL 1 HOUR', $ip) >= self::PER_IP_PER_HOUR) {
            return false;
        }

        $stmt = $this->db->prepare('SELECT id, status FROM subscribers WHERE email = ?');
        $stmt->execute([$email]);
        $subscriber = $stmt->fetch();

        // Already confirmed, or already sent a confirmation today: send nothing.
        // The page says the same thing either way, so it doesn't reveal who's on the list.
        if ($subscriber && $subscriber['status'] === 'confirmed') {
            return true;
        }
        if ($this->count('SELECT COUNT(*) FROM signup_mail WHERE email = ? AND sent_at > NOW() - INTERVAL 1 DAY', $email) > 0) {
            return true;
        }

        $token = bin2hex(random_bytes(32));
        $this->db->prepare(
            "INSERT INTO subscribers (email, status, confirm_token_hash, confirm_sent_at) VALUES (?, 'pending', ?, NOW())
             ON DUPLICATE KEY UPDATE status = 'pending', confirm_token_hash = VALUES(confirm_token_hash),
                                     confirm_sent_at = NOW(), unsubscribed_at = NULL"
        )->execute([$email, hash('sha256', $token)]);
        $this->db->prepare('INSERT INTO signup_mail (email, ip) VALUES (?, ?)')->execute([$email, $ip]);

        $this->mailer->send(
            (new Email())
                ->from('newsletter@example.com')
                ->to($email)
                ->subject('Confirm your subscription')
                ->text(
                    "Someone, hopefully you, asked to get our newsletter at this address.\n\n" .
                    "To confirm, open this link:\n{$this->siteUrl}/confirm.php?token=$token\n\n" .
                    "If it wasn't you, ignore this email. You won't hear from us again.\n"
                )
        );

        return true;
    }

    /** The link in the confirmation email was used. */
    public function confirm(string $token): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE subscribers SET status = 'confirmed', confirmed_at = NOW(), confirm_token_hash = NULL
              WHERE confirm_token_hash = ? AND status = 'pending'
                AND confirm_sent_at > NOW() - INTERVAL " . self::CONFIRM_DAYS . ' DAY'
        );
        $stmt->execute([hash('sha256', $token)]);

        return $stmt->rowCount() === 1;
    }

    /** The unsubscribe link for one subscriber: their id, signed, so nobody can make one for someone else. */
    public function unsubscribeUrl(int $id): string
    {
        return "{$this->siteUrl}/unsubscribe.php?id=$id&sig=" . $this->signature($id);
    }

    public function unsubscribe(int $id, string $sig): bool
    {
        if (!hash_equals($this->signature($id), $sig)) {
            return false;
        }
        $this->db->prepare("UPDATE subscribers SET status = 'unsubscribed', unsubscribed_at = NOW() WHERE id = ?")
            ->execute([$id]);

        return true;
    }

    private function signature(int $id): string
    {
        return hash_hmac('sha256', "unsubscribe|$id", $this->secret);
    }

    private function count(string $sql, string $value): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$value]);

        return (int) $stmt->fetchColumn();
    }
}
