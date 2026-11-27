<?php
declare(strict_types=1);

// Never trust input: two account ids and an amount in cents, all whole numbers.
$from = filter_var($argv[1] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$to = filter_var($argv[2] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$cents = filter_var($argv[3] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($from === false || $to === false || $cents === false || $from === $to) {
    fwrite(STDERR, "Usage: php {$argv[0]} FROM_ID TO_ID CENTS\n");
    exit(1);
}

return [$from, $to, $cents];
