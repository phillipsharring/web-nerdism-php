<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/Affiliates.php';

$affiliates = new Affiliates(connect());

// Arriving through an affiliate link: /?ref=ALICE10
if (isset($_GET['ref'])) {
    $affiliate = $affiliates->find($_GET['ref']);
    if ($affiliate !== null) {
        $affiliates->recordClick($affiliate['id']);
        setcookie(Affiliates::COOKIE, $affiliate['code'], [
            'expires' => time() + Affiliates::COOKIE_DAYS * 86400,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
    // Then to the same page without ?ref, so the code isn't bookmarked or
    // shared. The address is ours, fixed: never one taken from the request.
    header('Location: /', true, 302);
    exit;
}
?>
<!doctype html>
<meta charset="utf-8">
<title>The Widget</title>
<h1>The Widget</h1>
<p>It does the one thing you need. $49.99.</p>
<form method="post" action="/buy.php">
    <p><label>Email <input type="email" name="email" required></label></p>
    <p><button type="submit">Buy it</button></p>
</form>
