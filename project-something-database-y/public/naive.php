<?php
// The naive search: the query string is pasted straight into the SQL.
// Don't copy this one. Break It attacks it.
require dirname(__DIR__) . '/src/db.php';

$q = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? 'title';

$sql = "SELECT title, author, year FROM books WHERE title LIKE '%$q%' ORDER BY $sort";
$books = db()->query($sql)->fetchAll();
?>
<!doctype html>
<meta charset="utf-8">
<title>Books (naive)</title>
<form>
    <input name="q" value="<?= htmlspecialchars($q) ?>">
    <button type="submit">Search</button>
</form>
<table>
    <tr><th>Title</th><th>Author</th><th>Year</th></tr>
    <?php foreach ($books as $book): ?>
        <tr><td><?= htmlspecialchars($book['title']) ?></td><td><?= htmlspecialchars($book['author']) ?></td><td><?= $book['year'] ?></td></tr>
    <?php endforeach; ?>
</table>
