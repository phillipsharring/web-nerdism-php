<?php
declare(strict_types=1);

echo "Hello.\n";

// Too late: "Hello." has gone out, and the headers went out ahead of it.
header('X-Greeting: hi');
