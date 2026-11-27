<?php
declare(strict_types=1);

// The same message, "N products", for a few numbers in each language.
require __DIR__ . '/src/i18n.php';

foreach (array_keys(LANGUAGES) as $lang) {
    $messages = require __DIR__ . "/lang/$lang.php";
    $line = [];
    foreach ([0, 1, 2, 5, 12, 22, 25] as $n) {
        $line[] = translate($messages, $lang, 'products', ['count' => $n]);
    }
    echo "$lang: ", implode(' | ', $line), "\n";
}
