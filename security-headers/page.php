<?php
// The account page both versions show. It has a CSRF token, as it should;
// clickjacking gets past that, because the click happens on the real page.
declare(strict_types=1);

session_set_cookie_params(['secure' => true, 'httponly' => true, 'samesite' => 'Lax']);
ini_set('session.use_strict_mode', '1');
session_start();
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));

$deleted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? null;
    if (!is_string($token) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(403);
        exit("That form didn't come from this site.\n");
    }
    $deleted = true; // pretend
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Your account</title>

<?php if ($deleted): ?>
    <p><strong>Your account has been deleted.</strong> (Not really. It's an example.)</p>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
    <button type="submit" style="font-size: 2em">Delete my account</button>
</form>

<p id="inline">The inline script didn't run.</p>
<p id="file">app.js didn't run.</p>

<!-- An inline script, here to show what Content-Security-Policy does to one. -->
<script>document.getElementById('inline').textContent = 'The inline script ran.';</script>
<script src="/app.js"></script>
