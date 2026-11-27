<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/services.php';

$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Never trust input. And remember whose inbox this address is: maybe not
    // the person typing it.
    $email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
    if (($_POST['company'] ?? '') !== '') {
        // The honeypot was filled in: a bot. Act normal, send nothing.
        $message = 'Thanks! Check your inbox for a link to confirm.';
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($email) > 254) {
        http_response_code(422);
        $message = 'That doesn\'t look like an email address.';
    } elseif (!$subscriptions->subscribe(strtolower($email), $_SERVER['REMOTE_ADDR'])) {
        http_response_code(429);
        header('Retry-After: 3600');
        $message = 'That\'s a lot of signups. Try again later.';
    } else {
        $message = 'Thanks! Check your inbox for a link to confirm.';
    }
}
?>
<!doctype html>
<meta charset="utf-8">
<title>The Newsletter</title>
<style>.not-for-people { position: absolute; left: -10000px; }</style>
<h1>The Newsletter</h1>
<p>One email a month. Confirm your address to start; one click to stop.</p>
<?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>
<form method="post">
    <p><label>Email <input type="email" name="email" required maxlength="254"></label></p>
    <p class="not-for-people" aria-hidden="true">
        <label>Leave this empty <input name="company" tabindex="-1" autocomplete="off"></label>
    </p>
    <p><button type="submit">Subscribe</button></p>
</form>
