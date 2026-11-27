<?php

declare(strict_types=1);

// Every protected page has to remember to require this.
if (currentUser() === null) {
    http_response_code(401);
    exit("Log in first.\n");
}
