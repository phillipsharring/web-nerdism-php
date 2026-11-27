<?php

declare(strict_types=1);

$d = [
    ['alice@example.com', '2026-11-30', true],
    ['bob@example.com', '2026-12-02', false],
    ['carol@example.com', '2026-12-03', true],
    ['dan@example.com', '2027-01-15', true],
    ['erin@example.com', '2026-11-20', true],
];

$t = new DateTimeImmutable('2026-11-27');
$l = $t->modify('+7 days');
$arr = [];

foreach ($d as $x) {
    $tmp = new DateTimeImmutable($x[1]);
    if ($x[2] && $tmp >= $t && $tmp <= $l) {
        $arr[] = $x[0];
    }
}

echo 'Remind: ', implode(', ', $arr), "\n";
