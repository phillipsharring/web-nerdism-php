<?php
// The naive version: it pastes the email into the SQL. Don't copy this one.
declare(strict_types=1);

$pdo = require __DIR__ . '/db.php';
$email = $_GET['email'] ?? '';

header('Content-Type: text/plain; charset=utf-8');

$sql = "SELECT `name` FROM `members` WHERE `email` = '$email'";
echo "The query: $sql\n\n";

foreach ($pdo->query($sql) as $row) {
    echo 'Found: ', $row['name'], "\n";
}
