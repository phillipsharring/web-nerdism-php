<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/db.php';
require dirname(__DIR__) . '/src/posts.php';
require dirname(__DIR__) . '/src/feed.php';

const PER_PAGE = 3;

$db = connect();
$db->exec("SET time_zone = '+00:00'"); // the database and PHP both think in UTC
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$siteUrl = rtrim((string) getenv('SITE_URL'), '/');

/** Sends a 404 page and stops. */
function notFound(): never
{
    http_response_code(404);
    exit("<!doctype html>\n<meta charset=\"utf-8\">\n<title>Not found</title>\n<p>There's no post here.</p>\n");
}

// The feed: /feed
if ($path === '/feed') {
    header('Content-Type: application/atom+xml; charset=utf-8');
    echo atomFeed(latestPosts($db, 20, 0), $siteUrl);
    exit;
}

// One post: /posts/some-slug
if (preg_match('#^/posts/([a-z0-9-]{1,100})$#', $path, $m)) {
    $post = findPost($db, $m[1]) ?? notFound(); // a draft is a 404, same as a typo
    ?>
<!doctype html>
<meta charset="utf-8">
<title><?= htmlspecialchars($post['title']) ?></title>
<link rel="alternate" type="application/atom+xml" href="/feed" title="The Blog">
<p><a href="/">The Blog</a></p>
<article>
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <p><time datetime="<?= feedDate($post['published_at']) ?>"><?= niceDate($post['published_at']) ?></time></p>
    <?php foreach (paragraphs($post['body']) as $paragraph): ?>
        <p><?= nl2br(htmlspecialchars($paragraph)) ?></p>
    <?php endforeach; ?>
</article>
    <?php
    exit;
}

if ($path !== '/') {
    notFound();
}

// The list: /?page=2. Never trust input: the page is a whole number, 1 or
// more, or it's page 1. Past the last page is a 404.
$page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'default' => 1]]);
$pages = max(1, (int) ceil(countPosts($db) / PER_PAGE));
if ($page > $pages) {
    notFound();
}
$posts = latestPosts($db, PER_PAGE, ($page - 1) * PER_PAGE);
?>
<!doctype html>
<meta charset="utf-8">
<title>The Blog</title>
<link rel="alternate" type="application/atom+xml" href="/feed" title="The Blog">
<h1>The Blog</h1>
<?php foreach ($posts as $post): ?>
    <article>
        <h2><a href="/posts/<?= rawurlencode($post['slug']) ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
        <p><time datetime="<?= feedDate($post['published_at']) ?>"><?= niceDate($post['published_at']) ?></time></p>
        <p><?= htmlspecialchars(mb_strimwidth(paragraphs($post['body'])[0], 0, 200, '…')) ?></p>
    </article>
<?php endforeach; ?>
<nav aria-label="Pages">
    <?php if ($page > 1): ?><a href="/?page=<?= $page - 1 ?>" rel="prev">Newer</a><?php endif; ?>
    Page <?= $page ?> of <?= $pages ?>
    <?php if ($page < $pages): ?><a href="/?page=<?= $page + 1 ?>" rel="next">Older</a><?php endif; ?>
</nav>
