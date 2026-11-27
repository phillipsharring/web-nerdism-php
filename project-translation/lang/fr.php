<?php
// French puts a space before ! and ?; this one is a non-breaking space, so the
// ! never wraps onto a line of its own.
return [
    'title' => 'Notre boutique',
    'welcome' => "Bienvenue\u{00A0}!",
    'language' => 'Langue',
    'products' => ['one' => '{count} produit', 'other' => '{count} produits'],
];
