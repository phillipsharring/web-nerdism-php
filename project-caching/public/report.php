<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/FileCache.php';
require dirname(__DIR__) . '/src/report.php';

// Never trust input. The cache key is built only from a value we've checked,
// or anyone could fill the disk with one cache file per made-up region.
$region = $_GET['region'] ?? 'north';
if (!is_string($region) || !in_array($region, REGIONS, true)) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Pick a region: ' . implode(', ', REGIONS) . ".\n");
}

// Layer one: our own cache. The slow work happens at most once a minute.
$cache = new FileCache(dirname(__DIR__) . '/cache');
$report = $cache->remember("report:$region", 60, fn () => slowReport($region), $hit);
$body = json_encode($report, JSON_PRETTY_PRINT) . "\n";

header('X-Cache: ' . ($hit ? 'HIT' : 'MISS'));

// Layer two: the browser's cache, and any cache in the middle. The same as
// everyone's, so it's public; good for 60 seconds; and with an ETag, a
// fingerprint of the body, so after that a client can ask "has it changed?"
$etag = '"' . hash('sha256', $body) . '"';
header('Cache-Control: public, max-age=60');
header("ETag: $etag");

// The client already has this exact version: say so, and send no body.
$theirs = array_map('trim', explode(',', $_SERVER['HTTP_IF_NONE_MATCH'] ?? ''));
if (in_array($etag, $theirs, true)) {
    http_response_code(304);
    exit;
}

header('Content-Type: application/json');
echo $body;
