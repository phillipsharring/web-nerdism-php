<?php
// The naive guestbook: it stores what you type and prints it back as-is.
// Don't copy this one. Break It attacks it.
require dirname(__DIR__) . '/src/Guestbook.php';

$book = new Guestbook(dirname(__DIR__) . '/storage/entries.jsonl');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book->add([
        'name' => $_POST['name'] ?? '',
        'website' => $_POST['website'] ?? '',
        'message' => $_POST['message'] ?? '',
    ]);
    header('Location: /naive.php', true, 303);
    exit;
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Guestbook (naive)</title>
<h1>Guestbook</h1>
<form method="post">
    <p><label>Name <input name="name" required></label></p>
    <p><label>Website <input name="website"></label></p>
    <p><label>Message <textarea name="message" required></textarea></label></p>
    <p><button type="submit">Sign</button></p>
</form>
<?php foreach ($book->latest() as $entry): ?>
    <article>
        <h2><a href="<?= $entry['website'] ?>"><?= $entry['name'] ?></a></h2>
        <p><?= $entry['message'] ?></p>
    </article>
<?php endforeach; ?>
