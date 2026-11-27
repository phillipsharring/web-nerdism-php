<?php
declare(strict_types=1);

ob_start(); // From here on, output waits in a buffer instead of going out.

echo "Hello.\n";

// Fine: nothing has been sent yet, so the headers can still change.
header('X-Greeting: hi');

ob_end_flush(); // Now send it.
