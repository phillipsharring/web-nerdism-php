<?php
// The fixed version: the SQL and the email travel separately, so the email
// can only ever be a value, never part of the query.
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';
$email = $_GET['email'] ?? '';

header('Content-Type: text/plain; charset=utf-8');

// Never trust input. It has to be a string. (A real page would also check
// that it's an email address; that's left out so you can see the prepared
// statement do the work on its own.)
if (!is_string($email)) {
    http_response_code(422);
    exit("One email address, please.\n");
}

$statement = $pdo->prepare('SELECT `name` FROM `members` WHERE `email` = ?');
$statement->execute([$email]);
$rows = $statement->fetchAll();

if (!$rows) {
    echo "No member with that email.\n";
}
foreach ($rows as $row) {
    echo 'Found: ', $row['name'], "\n";
}
