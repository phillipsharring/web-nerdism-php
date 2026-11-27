<?php
declare(strict_types=1);

// A promise, with no code: anything that is a Notifier has this method.
interface Notifier
{
    public function send(string $to, string $message): void;
}
