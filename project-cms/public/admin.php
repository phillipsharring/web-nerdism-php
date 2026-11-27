<?php
declare(strict_types=1);

require dirname(__DIR__) . '/auth.php';
require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/csrf.php';
require dirname(__DIR__) . '/src/uploads.php';

requireBasicAuth(require dirname(__DIR__) . '/users.php', 'CMS');
header('Cache-Control: private, no-store');
startSession();
$db = connect();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();

    // Never trust input, even from your own editors' browsers.
    $slug = is_string($_POST['slug'] ?? null) ? trim($_POST['slug']) : '';
    $title = is_string($_POST['title'] ?? null) ? trim($_POST['title']) : '';
    $body = is_string($_POST['body'] ?? null) ? trim($_POST['body']) : '';

    if (!preg_match('/^[a-z0-9-]{1,60}$/', $slug)) {
        $errors[] = 'The address can only use a to z, 0 to 9 and hyphens.';
    }
    if ($title === '' || mb_strlen($title) > 200) {
        $errors[] = 'A title, please, up to 200 characters.';
    }
    if (mb_strlen($body) > 50000) {
        $errors[] = 'That page is too long.';
    }

    $image = null;
    $upload = $_FILES['image'] ?? null;
    if (!$errors && is_array($upload) && ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        try {
            $image = storeImage($upload, dirname(__DIR__) . '/storage/uploads');
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
    }

    if (!$errors) {
        $db->prepare(
            'INSERT INTO pages (slug, title, body, image) VALUES (:slug, :title, :body, :image)
             ON DUPLICATE KEY UPDATE title = VALUES(title), body = VALUES(body),
                                     image = COALESCE(VALUES(image), image)'
        )->execute(['slug' => $slug, 'title' => $title, 'body' => $body, 'image' => $image]);

        header('Location: /admin.php?saved=' . urlencode($slug), true, 303);
        exit;
    }
    $page = ['slug' => $slug, 'title' => $title, 'body' => $body];
} else {
    $stmt = $db->prepare('SELECT slug, title, body FROM pages WHERE slug = ?');
    $stmt->execute([is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '']);
    $page = $stmt->fetch() ?: ['slug' => '', 'title' => '', 'body' => ''];
}

$pages = $db->query('SELECT slug, title FROM pages ORDER BY slug')->fetchAll();
$saved = is_string($_GET['saved'] ?? null) ? $_GET['saved'] : null;
?>
<!doctype html>
<meta charset="utf-8">
<title>Pages</title>
<h1>Pages</h1>
<?php if ($saved !== null): ?>
    <p>Saved <a href="/<?= htmlspecialchars(rawurlencode($saved)) ?>"><?= htmlspecialchars($saved) ?></a>.</p>
<?php endif; ?>
<ul>
    <?php foreach ($pages as $p): ?>
        <li><a href="?edit=<?= urlencode($p['slug']) ?>"><?= htmlspecialchars($p['title']) ?></a></li>
    <?php endforeach; ?>
    <li><a href="?">New page</a></li>
</ul>

<?php foreach ($errors as $error): ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php endforeach; ?>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
    <p><label>Address <input name="slug" value="<?= htmlspecialchars($page['slug']) ?>" required pattern="[a-z0-9\-]+"></label></p>
    <p><label>Title <input name="title" value="<?= htmlspecialchars($page['title']) ?>" required maxlength="200"></label></p>
    <p><label>Text <textarea name="body" rows="10"><?= htmlspecialchars($page['body']) ?></textarea></label></p>
    <p><label>Image <input type="file" name="image" accept="image/png,image/jpeg,image/webp"></label></p>
    <p><button type="submit">Save</button></p>
</form>
