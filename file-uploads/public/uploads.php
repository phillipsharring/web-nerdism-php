<?php
// Shared by the fixed upload page and the page that serves the files.
declare(strict_types=1);

// Outside public/, so the web server can never hand these files out directly.
const STORAGE = __DIR__ . '/../storage';
const MAX_BYTES = 2 * 1024 * 1024;

// The types you accept, and the extension each one is saved with. The
// extension comes from this list, never from the upload.
const TYPES = [
    'image/png' => 'png',
    'image/jpeg' => 'jpg',
    'image/gif' => 'gif',
];
