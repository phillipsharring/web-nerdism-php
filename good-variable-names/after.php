<?php

declare(strict_types=1);

$subscriptions = [
    ['email' => 'alice@example.com', 'renewsOn' => '2026-11-30', 'autoRenews' => true],
    ['email' => 'bob@example.com', 'renewsOn' => '2026-12-02', 'autoRenews' => false],
    ['email' => 'carol@example.com', 'renewsOn' => '2026-12-03', 'autoRenews' => true],
    ['email' => 'dan@example.com', 'renewsOn' => '2027-01-15', 'autoRenews' => true],
    ['email' => 'erin@example.com', 'renewsOn' => '2026-11-20', 'autoRenews' => true],
];

$today = new DateTimeImmutable('2026-11-27');
$remindUntil = $today->modify('+7 days');
$emailsToRemind = [];

foreach ($subscriptions as $subscription) {
    $renewsOn = new DateTimeImmutable($subscription['renewsOn']);
    if ($subscription['autoRenews'] && $renewsOn >= $today && $renewsOn <= $remindUntil) {
        $emailsToRemind[] = $subscription['email'];
    }
}

echo 'Remind: ', implode(', ', $emailsToRemind), "\n";
