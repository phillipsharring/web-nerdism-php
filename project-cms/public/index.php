<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';

// /about shows the page whose slug is "about"; / shows "home".
$slug = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/', '/') ?: 'home';

$page = false;
if (preg_match('/^[a-z0-9-]{1,60}$/', $slug)) {
    $stmt = connect()->prepare('SELECT title, body, image FROM pages WHERE slug = ?');
    $stmt->execute([$slug]);
    $page = $stmt->fetch();
}
if ($page === false) {
    http_response_code(404);
    $page = ['title' => 'Not found', 'body' => 'There\'s no page here.', 'image' => null];
}

// The body is plain text: a blank line starts a new paragraph. Every piece is
// escaped, so whatever an editor types shows up as text, never as HTML.
$paragraphs = preg_split('/\R{2,}/', trim($page['body']));
?>
<!doctype html>
<meta charset="utf-8">
<title><?= htmlspecialchars($page['title']) ?></title>
<h1><?= htmlspecialchars($page['title']) ?></h1>
<?php if ($page['image'] !== null): ?>
    <img src="/image.php?name=<?= urlencode($page['image']) ?>" alt="">
<?php endif; ?>
<?php foreach ($paragraphs as $paragraph): ?>
    <p><?= nl2br(htmlspecialchars($paragraph)) ?></p>
<?php endforeach; ?>
