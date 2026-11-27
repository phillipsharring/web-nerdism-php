<?php
declare(strict_types=1);

function start_session(): void
{
    session_start([
        'use_strict_mode' => true,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'cookie_secure' => ($_SERVER['HTTPS'] ?? 'off') !== 'off',
    ]);
}

function redirect(string $to): never
{
    header('Location: ' . $to, true, 303);
    exit;
}

// The signed-in member, or off to the sign-in page. A session from before
// the last password change doesn't count: that's how changing the password
// signs out everyone else.
function require_login(PDO $db): array
{
    $id = $_SESSION['user_id'] ?? null;
    $user = false;
    if ($id !== null) {
        $stmt = $db->prepare('SELECT id, email, password_hash, session_version FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();
    }
    if ($user === false || (int) $user['session_version'] !== (int) ($_SESSION['session_version'] ?? 0)) {
        $_SESSION = [];
        redirect('/login.php');
    }
    header('Cache-Control: private, no-store');

    return $user;
}
