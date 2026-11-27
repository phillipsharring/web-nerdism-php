<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/services.php';

// GET shows a button. POST unsubscribes: from that button, or from a mail
// program's own Unsubscribe button, which POSTs "List-Unsubscribe=One-Click".
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$sig = is_string($_GET['sig'] ?? null) ? $_GET['sig'] : '';
$done = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $done = $id !== false && $subscriptions->unsubscribe($id, $sig);
    http_response_code($done ? 200 : 400);
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Unsubscribe</title>
<?php if ($done === true): ?>
    <p>Done. You won't get the newsletter any more.</p>
<?php elseif ($done === false): ?>
    <p>That link isn't right. Use the one at the bottom of the newsletter.</p>
<?php else: ?>
    <form method="post">
        <p><button type="submit">Unsubscribe</button></p>
    </form>
<?php endif; ?>
