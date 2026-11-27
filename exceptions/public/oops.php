<?php
declare(strict_types=1);

require dirname(__DIR__) . '/handler.php';

// Something nobody planned for. No try, no catch: it goes straight to the handler.
throw new RuntimeException('Pretend the database is down.');
