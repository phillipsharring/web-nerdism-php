<?php

declare(strict_types=1);

final class WriteAuditLog
{
    public function __invoke(UserSignedUp $event): void
    {
        echo "  audit log: signup $event->email\n";
    }
}
