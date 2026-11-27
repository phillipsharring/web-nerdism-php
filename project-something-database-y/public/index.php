<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';

// Column names can't be placeholders, so sorting is an allow list: the
// request picks a key, and the SQL comes from here.
$sorts = [
    'title' => 'title',
    'author' => 'author, title',
    'year' => 'year, title',
];

// Never trust input. Both values come from the query string.
$q = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$q = mb_substr($q, 0, 100);
$sort = is_string($_GET['sort'] ?? null) && isset($sorts[$_GET['sort']]) ? $_GET['sort'] : 'title';

// In LIKE, % and _ are wildcards. Escape them, so a search for "100%" means
// those four characters.
$pattern = '%' . addcslashes($q, '%_\\') . '%';

try {
    $stmt = db()->prepare("SELECT title, author, year FROM books WHERE title LIKE :pattern ORDER BY {$sorts[$sort]}");
    $stmt->execute(['pattern' => $pattern]);
    $books = $stmt->fetchAll();
} catch (PDOException $e) {
    // The details go to the log, not the page.
    error_log($e->getMessage());
    http_response_code(500);
    exit('Sorry, the search is broken right now.');
}

$e = fn (string $text): string => htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<meta charset="utf-8">
<title>Books</title>
<form>
    <input name="q" value="<?= $e($q) ?>" maxlength="100">
    <select name="sort">
        <?php foreach (array_keys($sorts) as $key): ?>
            <option<?= $key === $sort ? ' selected' : '' ?>><?= $e($key) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Search</button>
</form>
<?php if (!$books): ?>
    <p>No books match.</p>
<?php else: ?>
    <table>
        <tr><th>Title</th><th>Author</th><th>Year</th></tr>
        <?php foreach ($books as $book): ?>
            <tr><td><?= $e($book['title']) ?></td><td><?= $e($book['author']) ?></td><td><?= $book['year'] ?></td></tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
