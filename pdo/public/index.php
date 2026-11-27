<?php
declare(strict_types=1);

$pdo = require dirname(__DIR__) . '/db.php';

// Adding a book. Never trust input: check every field, then hand it to MySQL
// as a value, separate from the SQL. It is never glued into the query.
$errors = [];
$title = '';
$author = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = is_string($_POST['title'] ?? null) ? trim($_POST['title']) : '';
    $author = is_string($_POST['author'] ?? null) ? trim($_POST['author']) : '';
    if ($title === '' || !mb_check_encoding($title, 'UTF-8') || mb_strlen($title) > 200) {
        $errors[] = 'A title, please, up to 200 characters.';
    }
    if ($author === '' || !mb_check_encoding($author, 'UTF-8') || mb_strlen($author) > 100) {
        $errors[] = 'An author, please, up to 100 characters.';
    }
    if (!$errors) {
        $insert = $pdo->prepare('INSERT INTO books (title, author) VALUES (:title, :author)');
        $insert->execute(['title' => $title, 'author' => $author]);
        header('Location: /?added=' . $pdo->lastInsertId(), true, 303);
        exit;
    }
}

// Searching. The words go in as a value too. % and _ mean "anything" in LIKE,
// so they're escaped, and a search for % looks for a real percent sign.
$q = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$pattern = '%' . addcslashes($q, '%_\\') . '%';
$search = $pdo->prepare(
    'SELECT id, title, author FROM books
     WHERE title LIKE :in_title OR author LIKE :in_author
     ORDER BY title'
);
$search->execute(['in_title' => $pattern, 'in_author' => $pattern]);
$books = $search->fetchAll();
?>
<!doctype html>
<html lang="en">
<meta charset="utf-8">
<title>Library</title>

<form>
    <label>Search <input name="q" value="<?= htmlspecialchars($q) ?>"></label>
    <button type="submit">Search</button>
</form>

<p><?= count($books) ?> <?= count($books) === 1 ? 'book' : 'books' ?></p>
<ul>
<?php foreach ($books as $book): ?>
    <li>#<?= $book['id'] ?>: <?= htmlspecialchars($book['title']) ?>, by <?= htmlspecialchars($book['author']) ?></li>
<?php endforeach; ?>
</ul>

<?php foreach ($errors as $error): ?>
    <p><?= $error ?></p>
<?php endforeach; ?>
<form method="post">
    <p><label>Title <input name="title" value="<?= htmlspecialchars($title) ?>" required maxlength="200"></label></p>
    <p><label>Author <input name="author" value="<?= htmlspecialchars($author) ?>" required maxlength="100"></label></p>
    <p><button type="submit">Add a book</button></p>
</form>
