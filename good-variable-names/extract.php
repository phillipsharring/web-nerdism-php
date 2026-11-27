<?php

declare(strict_types=1);

// Never do this. extract() turns every key in an array into a variable,
// and $_GET's keys come from whoever typed the URL.
$isAdmin = false;
extract($_GET);

header('Content-Type: text/plain');
echo $isAdmin ? "Welcome, administrator.\n" : "Welcome, guest.\n";
