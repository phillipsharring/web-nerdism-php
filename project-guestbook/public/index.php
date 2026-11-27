<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/Guestbook.php';
require dirname(__DIR__) . '/src/functions.php';

// A second line of defense: the browser runs no script that didn't come
// from this site as a file. Inline <script> and onerror="..." are refused.
header("Content-Security-Policy: default-src 'self'");

$book = new Guestbook(dirname(__DIR__) . '/storage/entries.jsonl');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Never trust input: strings only, valid UTF-8, sensible lengths.
    $field = function (string $name, int $max): string {
        $value = $_POST[$name] ?? '';
        $ok = is_string($value) && mb_check_encoding($value, 'UTF-8') && mb_strlen(trim($value)) <= $max;

        return $ok ? trim($value) : '';
    };
    $name = $field('name', 50);
    $website = $field('website', 200);
    $message = $field('message', 1000);

    if ($name === '') {
        $errors[] = 'Your name, please, up to 50 characters.';
    }
    if ($message === '') {
        $errors[] = 'A message, please, up to 1,000 characters.';
    }
    if ($website !== '' && web_url($website) === '') {
        $errors[] = 'A website has to start with http:// or https://.';
    }

    if (!$errors) {
        $book->add(['name' => $name, 'website' => $website, 'message' => $message]);
        header('Location: /', true, 303);
        exit;
    }
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Guestbook</title>
<h1>Guestbook</h1>
<?php foreach ($errors as $error): ?>
    <p><?= e($error) ?></p>
<?php endforeach; ?>
<form method="post">
    <p><label>Name <input name="name" maxlength="50" required></label></p>
    <p><label>Website <input type="url" name="website" maxlength="200"></label></p>
    <p><label>Message <textarea name="message" maxlength="1000" required></textarea></label></p>
    <p><button type="submit">Sign</button></p>
</form>
<?php foreach ($book->latest() as $entry): ?>
    <article>
        <?php $url = web_url((string) $entry['website']); ?>
        <h2>
            <?php if ($url !== ''): ?>
                <a href="<?= e($url) ?>" rel="nofollow ugc"><?= e((string) $entry['name']) ?></a>
            <?php else: ?>
                <?= e((string) $entry['name']) ?>
            <?php endif; ?>
        </h2>
        <p><?= nl2br(e((string) $entry['message'])) ?></p>
    </article>
<?php endforeach; ?>
