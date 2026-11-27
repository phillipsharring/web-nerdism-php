<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/spam.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

$db = connect();
$ip = $_SERVER['REMOTE_ADDR']; // behind a proxy, see What's in the Middle

/** Stops with a status and a message the person can act on. */
function refuse(int $status, string $message): never
{
    http_response_code($status);
    header('Content-Type: text/plain; charset=utf-8');
    exit($message . "\n");
}

// Never trust input. Every field is checked: type, length, and valid UTF-8.
$postId = filter_var($_POST['post'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$name = is_string($_POST['name'] ?? null) ? trim($_POST['name']) : '';
$body = is_string($_POST['body'] ?? null) ? trim($_POST['body']) : '';

$exists = $db->prepare('SELECT 1 FROM posts WHERE id = ?');
$exists->execute([$postId === false ? 0 : $postId]);
if (!$exists->fetch()) {
    refuse(404, 'There\'s no post to comment on.');
}

// The honeypot was filled in: a bot. Say thanks, store nothing, teach it nothing.
if (($_POST['website'] ?? '') !== '') {
    header('Location: /?note=thanks#comments', true, 303);
    exit;
}

if (!tokenIsGood($_POST['t'] ?? null, $postId, getenv('COMMENT_SECRET'))) {
    refuse(400, 'That was quick, or the page is old. Reload it and try again.');
}
if (!mb_check_encoding($name . $body, 'UTF-8')) {
    refuse(400, 'That text isn\'t valid UTF-8.');
}
if ($name === '' || mb_strlen($name) > 80 || $body === '' || mb_strlen($body) > 5000) {
    refuse(422, 'A name (up to 80 characters) and a comment (up to 5,000), please.');
}
if (tooMany($db, $ip)) {
    header('Retry-After: 600');
    refuse(429, 'That\'s a lot of comments. Try again in a few minutes.');
}

$approved = needsALook($body) ? 0 : 1;
$db->prepare('INSERT INTO comments (post_id, name, body, ip, approved) VALUES (?, ?, ?, ?, ?)')
    ->execute([$postId, $name, $body, $ip, $approved]);

// Stored exactly as typed. Escaping happens on the way out, in index.php.
header('Location: /?note=' . ($approved ? 'thanks' : 'held') . '#comments', true, 303);
