<?php
declare(strict_types=1);

include __DIR__ . '/no-such-file.php';
echo "After include: still running\n";

require __DIR__ . '/no-such-file.php';
echo "After require: you never see this\n";
