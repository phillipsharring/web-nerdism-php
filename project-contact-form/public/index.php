<?php
declare(strict_types=1);

use App\FileRateLimiter;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

require dirname(__DIR__) . '/src/bootstrap.php';

const OWNER = 'owner@example.com';
const SITE = 'hello@example.com';

$storage = dirname(__DIR__) . '/storage/rate-limits';
$perVisitor = new FileRateLimiter($storage . '/visitor', 3, 600);   // 3 per 10 minutes each
$perSite = new FileRateLimiter($storage . '/site', 20, 3600);      // 20 an hour, from everyone

// Never trust input: every field is checked for type, length and encoding.
function field(string $name, int $max): string
{
    $value = $_POST[$name] ?? '';
    if (!is_string($value) || !mb_check_encoding($value, 'UTF-8')) {
        return '';
    }
    $value = trim($value);

    return mb_strlen($value) <= $max ? $value : '';
}

$errors = [];
$name = $email = $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = field('name', 100);
    $email = field('email', 254);
    $message = field('message', 5000);

    // A field people can't see. Bots fill in everything; people leave it empty.
    if (($_POST['website'] ?? '') !== '') {
        header('Location: /?sent=1', true, 303);
        exit;
    }

    if ($name === '') {
        $errors[] = 'Your name, please, up to 100 characters.';
    }
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'An email address we can reply to, please.';
    }
    if ($message === '') {
        $errors[] = 'A message, please, up to 5,000 characters.';
    }

    if (!$errors) {
        // Behind a proxy, REMOTE_ADDR is the proxy. See What's in the Middle.
        if (!$perVisitor->attempt($_SERVER['REMOTE_ADDR']) || !$perSite->attempt('everyone')) {
            http_response_code(429);
            header('Retry-After: 600');
            $errors[] = "That's a lot of messages. Please try again in a few minutes.";
        } else {
            // Only ever to you. From you. The visitor goes in Reply-To.
            $mailer->send((new Email())
                ->from(new Address(SITE, 'Contact form'))
                ->to(OWNER)
                ->replyTo(new Address($email, $name))
                ->subject('Contact form message')
                ->text("From: $name <$email>\n\n$message\n"));

            header('Location: /?sent=1', true, 303);
            exit;
        }
    }
}

$e = fn (string $text): string => htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<meta charset="utf-8">
<title>Contact</title>
<?php if (isset($_GET['sent'])): ?>
    <p>Thanks. Your message is on its way.</p>
<?php endif; ?>
<?php foreach ($errors as $error): ?>
    <p><?= $e($error) ?></p>
<?php endforeach; ?>
<form method="post">
    <p><label>Name <input name="name" value="<?= $e($name) ?>" maxlength="100" required></label></p>
    <p><label>Email <input type="email" name="email" value="<?= $e($email) ?>" maxlength="254" required></label></p>
    <p><label>Message <textarea name="message" maxlength="5000" required><?= $e($message) ?></textarea></label></p>
    <p hidden><label>Leave this empty <input name="website" tabindex="-1" autocomplete="off"></label></p>
    <p><button type="submit">Send</button></p>
</form>
