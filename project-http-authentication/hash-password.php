<?php
declare(strict_types=1);

// Reads a password from the keyboard (not the command line, where it would
// land in your shell history) and prints its hash for users.php.
echo 'Password: ';
$password = trim((string) fgets(STDIN));
if ($password === '') {
    fwrite(STDERR, "No password given.\n");
    exit(1);
}
echo password_hash($password, PASSWORD_DEFAULT), "\n";
