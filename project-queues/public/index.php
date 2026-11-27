<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Database;
use App\Queue;

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Never trust input: check the address before it goes anywhere.
    $email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($email) > 254) {
        $error = 'That doesn\'t look like an email address.';
    } else {
        // Don't send the email here. Write down that it needs sending, and
        // answer at once; a worker does the slow part.
        $queue = new Queue((new Database())->connect());
        $queue->push('welcome-email', ['email' => $email]);
        header('Location: /?thanks=1', true, 303);
        exit;
    }
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Sign up</title>
<?php if (isset($_GET['thanks'])): ?>
    <p>Thanks! A welcome email is on its way.</p>
<?php endif; ?>
<?php if ($error): ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form method="post">
    <p><label>Email <input type="email" name="email" required></label></p>
    <p><button type="submit">Sign up</button></p>
</form>
