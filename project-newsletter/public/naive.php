<?php
// BROKEN ON PURPOSE, for the Break It section. Never put this on a server.
// Whatever address is typed goes straight on the list, and gets an email at
// once. Every time. From anyone.
require dirname(__DIR__) . '/src/services.php';

use Symfony\Component\Mime\Email;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $db->prepare("INSERT INTO subscribers (email, status) VALUES (?, 'confirmed') ON DUPLICATE KEY UPDATE status = 'confirmed'")
        ->execute([$email]);
    $mailer->send((new Email())->from('newsletter@example.com')->to($email)
        ->subject('Welcome to the newsletter!')->text("You're on the list.\n"));
    echo "Subscribed!\n";
}
