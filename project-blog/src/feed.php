<?php
declare(strict_types=1);

/**
 * An Atom feed of the latest posts. XMLWriter escapes every value it writes,
 * so a title like "Escaping <b>output</b>" can't break the XML.
 *
 * @param list<array<string, string>> $posts
 */
function atomFeed(array $posts, string $siteUrl): string
{
    $xml = new XMLWriter();
    $xml->openMemory();
    $xml->startDocument('1.0', 'UTF-8');
    $xml->startElementNs(null, 'feed', 'http://www.w3.org/2005/Atom');
    $xml->writeElement('title', 'The Blog');
    $xml->writeElement('id', "$siteUrl/");
    $xml->writeElement('updated', feedDate($posts[0]['published_at'] ?? '2026-01-01 00:00:00'));
    $xml->startElement('link');
    $xml->writeAttribute('rel', 'self');
    $xml->writeAttribute('href', "$siteUrl/feed");
    $xml->endElement();
    $xml->startElement('author');
    $xml->writeElement('name', 'The Blog');
    $xml->endElement();

    foreach ($posts as $post) {
        $url = "$siteUrl/posts/" . rawurlencode($post['slug']);
        $xml->startElement('entry');
        $xml->writeElement('title', $post['title']);
        $xml->writeElement('id', $url);
        $xml->writeElement('updated', feedDate($post['published_at']));
        $xml->startElement('link');
        $xml->writeAttribute('href', $url);
        $xml->endElement();
        $xml->startElement('content');
        $xml->writeAttribute('type', 'text');
        $xml->text($post['body']);
        $xml->endElement();
        $xml->endElement();
    }

    $xml->endElement();
    $xml->endDocument();

    return $xml->outputMemory();
}
