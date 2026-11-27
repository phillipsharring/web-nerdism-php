<?php
// The naive contact form. It works, and it's a spam cannon: no checks, no
// limits, the visitor's address as From, and a copy to any address typed in.
// Don't copy this one. Break It attacks it.
use Symfony\Component\Mime\Email;

require dirname(__DIR__) . '/src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = (new Email())
        ->from($_POST['email'])
        ->to('owner@example.com')
        ->subject('Message from ' . $_POST['name'])
        ->text($_POST['message']);
    $mailer->send($email);

    if (isset($_POST['copy'])) {
        $mailer->send((clone $email)->to($_POST['email'])->subject('Your message to us'));
    }
    echo '<p>Thanks! Your message was sent.</p>';
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Contact (naive)</title>
<form method="post">
    <p><label>Name <input name="name" required></label></p>
    <p><label>Email <input type="email" name="email" required></label></p>
    <p><label>Message <textarea name="message" required></textarea></label></p>
    <p><label><input type="checkbox" name="copy"> Send me a copy</label></p>
    <p><button type="submit">Send</button></p>
</form>
