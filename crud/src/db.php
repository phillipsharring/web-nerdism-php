<?php
declare(strict_types=1);

// One connection, set up the way every query in the course expects.
// The settings come from the environment, never from the code.
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', getenv('DB_HOST'), getenv('DB_NAME'));

return new PDO($dsn, getenv('DB_USER'), getenv('DB_PASSWORD'), [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,      // a failed query throws (the default since PHP 8)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // each row as ['column' => value]
    PDO::ATTR_EMULATE_PREPARES => false,              // real prepared statements, done by MySQL
]);
