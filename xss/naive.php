<?php
// The naive version: it puts input into the page as it came. Don't copy this one.
declare(strict_types=1);

// A pretend login cookie, without HttpOnly, so the page's JavaScript can read it.
setcookie('session', 'pretend-session-id');

$name = $_GET['name'] ?? 'stranger';
$website = $_GET['website'] ?? 'https://example.com';
?>
<!doctype html>
<meta charset="utf-8">
<title>Profile preview (naive)</title>

<p>Hello, <?= $name ?>.</p>
<p><a href="<?= $website ?>">Your website</a></p>

<form>
    <p><label>Name <input name="name" value="<?= $name ?>"></label></p>
    <p><label>Website <input name="website" value="<?= $website ?>"></label></p>
    <p><button type="submit">Preview</button></p>
</form>
