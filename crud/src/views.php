<?php

declare(strict_types=1);

function e(string $text): string
{
    return htmlspecialchars($text);
}

function page(string $title, string $body): void
{
    echo "<!doctype html>\n<meta charset=\"utf-8\">\n<title>", e($title), "</title>\n", $body;
}

function listPage(array $notes, ?string $error = null): void
{
    $html = '<h1>Notes</h1>' . ($error ? '<p>' . e($error) . '</p>' : '') . "\n<ul>\n";
    foreach ($notes as $note) {
        $html .= '<li>' . e($note['body'])
            . ' <a href="/notes/' . $note['id'] . '/edit">edit</a>'
            . ' <form method="post" action="/notes/' . $note['id'] . '/delete" style="display:inline">'
            . '<button type="submit">delete</button></form></li>' . "\n";
    }
    $html .= "</ul>\n"
        . '<form method="post" action="/notes"><input name="body" maxlength="500" required> '
        . '<button type="submit">Add</button></form>' . "\n";
    page('Notes', $html);
}

function editPage(array $note, ?string $error = null): void
{
    page('Edit note', '<h1>Edit note</h1>' . ($error ? '<p>' . e($error) . '</p>' : '') . "\n"
        . '<form method="post" action="/notes/' . $note['id'] . '">'
        . '<input name="body" maxlength="500" required value="' . e($note['body']) . '"> '
        . '<button type="submit">Save</button></form>' . "\n");
}
