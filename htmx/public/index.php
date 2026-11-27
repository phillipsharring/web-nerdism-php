<?php
declare(strict_types=1);

$colors = [
    'Black', 'Blue', 'Brown', 'Gray', 'Green', 'Navy',
    'Orange', 'Pink', 'Purple', 'Red', 'White', 'Yellow',
];

// Never trust input: the search is whatever the visitor typed.
$q = is_string($_GET['q'] ?? null) ? trim(mb_substr($_GET['q'], 0, 50)) : '';
$matches = [];
foreach ($colors as $color) {
    if ($q !== '' && stripos($color, $q) === 0) {
        $matches[] = $color;
    }
}

function results(array $matches): void
{
    foreach ($matches as $color) {
        echo '<li>', htmlspecialchars($color), "</li>\n";
    }
}

// The same URL answers two ways, so caches must keep them apart.
header('Vary: HX-Request');

// htmx says so when it's asking. Then send only the part that changes.
if (($_SERVER['HTTP_HX_REQUEST'] ?? '') === 'true') {
    results($matches);
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Find a color</title>
    <script src="https://unpkg.com/htmx.org@2.0.4/dist/htmx.min.js"
        integrity="sha384-HGfztofotfshcF7+8n44JQL2oJmowVChPTg48S+jvZoztPfvwD79OC/LTtG6dMp+"
        crossorigin="anonymous"></script>
</head>
<body>
    <h1>Find a color</h1>

    <!-- Without JavaScript, an ordinary form. With htmx, typing updates the list. -->
    <form action="/" method="get">
        <label>Color
            <input name="q" value="<?= htmlspecialchars($q) ?>" autocomplete="off"
                hx-get="/" hx-trigger="input changed delay:300ms" hx-target="#results">
        </label>
        <button type="submit">Search</button>
    </form>

    <ul id="results">
        <?php results($matches); ?>
    </ul>
</body>
</html>
