<?php
declare(strict_types=1);

// Connects twice: once saying latin1 (wrong), once saying utf8mb4 (right).
function connect(string $charset): PDO
{
    return new PDO(
        sprintf('mysql:host=%s;dbname=%s;charset=%s', getenv('DB_HOST'), getenv('DB_NAME'), $charset),
        getenv('DB_USER'),
        getenv('DB_PASSWORD'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
    );
}

$wrong = connect('latin1');
$right = connect('utf8mb4');

// PHP sends the UTF-8 bytes for "café", but tells MySQL they're latin1.
$wrong->prepare('
    INSERT INTO `notes` (`body`)
    VALUES (?)
')->execute(['café']);
$id = $wrong->lastInsertId();

$sql = '
    SELECT `body`, HEX(`body`)
    FROM `notes`
    WHERE `id` = ?
';

$read = $wrong->prepare($sql);
$read->execute([$id]);
[$body] = $read->fetch(PDO::FETCH_NUM);
echo "Read back the wrong way: $body\n";

$read = $right->prepare($sql);
$read->execute([$id]);
[$body, $hex] = $read->fetch(PDO::FETCH_NUM);
echo "Read back the right way: $body\n";
echo "What's on disk:          $hex\n";
