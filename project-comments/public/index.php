<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/spam.php';

$db = connect();
$post = $db->query('SELECT id, title, body FROM posts WHERE id = 1')->fetch();
$stmt = $db->prepare('SELECT name, body, created_at FROM comments WHERE post_id = ? AND approved = 1 ORDER BY id');
$stmt->execute([$post['id']]);
$comments = $stmt->fetchAll();

$notes = ['thanks' => 'Thanks! Your comment is up.', 'held' => 'Thanks! Your comment will appear once someone has read it.'];
$note = $notes[$_GET['note'] ?? ''] ?? null; // only our own messages, never text from the URL
?>
<!doctype html>
<meta charset="utf-8">
<title><?= htmlspecialchars($post['title']) ?></title>
<style>.not-for-people { position: absolute; left: -10000px; }</style>

<h1><?= htmlspecialchars($post['title']) ?></h1>
<p><?= htmlspecialchars($post['body']) ?></p>

<h2 id="comments">Comments</h2>
<?php if ($note): ?><p><?= $note ?></p><?php endif; ?>
<?php foreach ($comments as $comment): ?>
    <article>
        <p><strong><?= htmlspecialchars($comment['name']) ?></strong> wrote:</p>
        <p><?= nl2br(htmlspecialchars($comment['body'])) ?></p>
    </article>
<?php endforeach; ?>

<form method="post" action="/comment.php">
    <input type="hidden" name="post" value="<?= $post['id'] ?>">
    <input type="hidden" name="t" value="<?= formToken($post['id'], getenv('COMMENT_SECRET')) ?>">
    <p><label>Name <input name="name" required maxlength="80"></label></p>
    <p><label>Comment <textarea name="body" required maxlength="5000" rows="5"></textarea></label></p>
    <!-- The honeypot: hidden from people, irresistible to bots that fill in every field. -->
    <p class="not-for-people" aria-hidden="true">
        <label>Leave this empty <input name="website" tabindex="-1" autocomplete="off"></label>
    </p>
    <p><button type="submit">Post comment</button></p>
</form>
