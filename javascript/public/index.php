<?php
declare(strict_types=1);

// Never trust input. The name goes into the page twice: as text, and as data
// for the script.
$name = is_string($_GET['name'] ?? null) ? trim($_GET['name']) : '';
$name = $name === '' ? 'friend' : $name;
$sizes = ['Small', 'Medium', 'Large'];

// With these flags, json_encode() turns < > & ' and " into \u codes, so the
// data can't close the script tag it sits in.
$data = json_encode(
    ['name' => $name, 'sizes' => $sizes],
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR,
);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shirts</title>
    <script src="/app.js" defer></script>
</head>
<body>
    <h1>Hello, <?= htmlspecialchars($name) ?></h1>
    <p><label>Size <select id="size"></select></label></p>
    <p id="choice"></p>

    <script type="application/json" id="data"><?= $data ?></script>
</body>
</html>
