<?php
declare(strict_types=1);

namespace Shop;

use PDO;

/** Makes the connection, from settings in the environment. */
final class Database
{
    public function connect(): PDO
    {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', getenv('DB_HOST'), getenv('DB_NAME'));

        return new PDO($dsn, getenv('DB_USER'), getenv('DB_PASSWORD'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}
