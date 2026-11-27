<?php
// The fixed version: the cookie gets its flags, PHP refuses session ids it
// didn't make, and logging in starts a new session id.
declare(strict_types=1);

require __DIR__ . '/login.php';

session_set_cookie_params([
    'lifetime' => 0,      // gone when the browser closes
    'path' => '/',
    'secure' => true,     // HTTPS only. Always on: never worked out from the request.
    'httponly' => true,   // JavaScript can't read it
    'samesite' => 'Lax',  // not sent on POSTs from other sites
]);
ini_set('session.use_strict_mode', '1'); // an id PHP didn't make gets replaced
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = checkLogin();
    if ($email !== null) {
        // New id, old one deleted: whoever knew the id before login knows nothing now.
        session_regenerate_id(true);
        $_SESSION['email'] = $email;
    } else {
        http_response_code(401);
    }
}
$who = $_SESSION['email'] ?? null;
?>
<!doctype html>
<meta charset="utf-8">
<title>Members</title>

<p><?= $who !== null ? 'Logged in as ' . e($who) . '.' : 'Not logged in.' ?></p>

<form method="post">
    <p><label>Email <input type="email" name="email" required></label></p>
    <p><label>Password <input type="password" name="password" required></label></p>
    <p><button type="submit">Log in</button></p>
</form>
