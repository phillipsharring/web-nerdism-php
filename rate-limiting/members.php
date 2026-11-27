<?php
// Shared by both versions: the password check.
declare(strict_types=1);

// Never trust input: both fields must be strings. Then the usual check.
function passwordIsRight(PDO $pdo, mixed $email, mixed $password): bool
{
    if (!is_string($email) || !is_string($password)) {
        return false;
    }
    $statement = $pdo->prepare('SELECT `password_hash` FROM `members` WHERE `email` = ?');
    $statement->execute([$email]);
    $hash = $statement->fetchColumn();

    return is_string($hash) && password_verify($password, $hash);
}
