<?php
declare(strict_types=1);

// How many guesses per second, md5() against password_hash()?
$count = function (callable $guess): int {
    $n = 0;
    $end = microtime(true) + 1;
    while (microtime(true) < $end) {
        $guess("guess$n");
        $n++;
    }

    return $n;
};

$hash = password_hash('correct horse battery staple', PASSWORD_DEFAULT);

printf("md5:            %s guesses a second\n", number_format($count(fn (string $p) => md5($p))));
printf("password_hash:  %s guesses a second\n", number_format($count(fn (string $p) => password_verify($p, $hash))));
