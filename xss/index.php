<?php
// The fixed version. Never trust input: everything from the request is escaped
// on its way into the page, and the link has to be a web address.
declare(strict_types=1);

// Escape for HTML: <, >, &, " and ' become harmless text. Use it on every
// value you print, in the page and in attributes.
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Escaping can't make javascript:alert(1) safe in an href; it has no special
// characters to escape. So a link must start with http:// or https://.
function webAddress(string $value): ?string
{
    $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
    $valid = filter_var($value, FILTER_VALIDATE_URL) !== false;

    return $valid && in_array($scheme, ['http', 'https'], true) ? $value : null;
}

$name = is_string($_GET['name'] ?? null) ? $_GET['name'] : 'stranger';
$website = is_string($_GET['website'] ?? null) ? $_GET['website'] : 'https://example.com';
$link = webAddress($website);
?>
<!doctype html>
<meta charset="utf-8">
<title>Profile preview</title>

<p>Hello, <?= e($name) ?>.</p>
<?php if ($link !== null): ?>
    <p><a href="<?= e($link) ?>">Your website</a></p>
<?php else: ?>
    <p>That website isn't a web address, so it isn't linked.</p>
<?php endif; ?>

<form>
    <p><label>Name <input name="name" value="<?= e($name) ?>"></label></p>
    <p><label>Website <input name="website" value="<?= e($website) ?>"></label></p>
    <p><button type="submit">Preview</button></p>
</form>
