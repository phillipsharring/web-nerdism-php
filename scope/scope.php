<?php
declare(strict_types=1);

$site = 'Example Shop'; // set outside any function: the global scope

function welcome(): string
{
    // A function starts with no variables of its own. This $site isn't the one above.
    return isset($site) ? 'Welcome to ' . $site : 'welcome() cannot see $site';
}

function welcome_to(string $site): string
{
    // Pass in what a function needs. Now it has its own $site, a copy.
    return 'Welcome to ' . $site;
}

function count_visit(): int
{
    $visits = 1; // made in here, gone when the function returns
    return $visits;
}

echo welcome(), "\n";
echo welcome_to($site), "\n";
count_visit();
echo isset($visits) ? 'Visits leaked out' : 'Visits stayed inside count_visit()', "\n";

// Superglobals, like $_SERVER and $_GET, are visible everywhere.
function script(): string
{
    return $_SERVER['SCRIPT_NAME'];
}
echo 'Running ', script(), "\n";

// An if or a loop is not a scope. What's made inside is still there after.
foreach (['small', 'medium', 'large'] as $size) {
    $last = $size;
}
echo 'After the loop: $size is ', $size, ', $last is ', $last, "\n";

// A closure gets outside variables only if you hand them over with use().
// An arrow function (fn) takes the ones it uses automatically. Both take copies.
$greeting = 'Hi';
$closure = function (string $name) use ($greeting): string {
    return $greeting . ', ' . $name;
};
$arrow = fn(string $name): string => $greeting . ', ' . $name;
$greeting = 'Goodbye';
echo $closure('Alice'), ' / ', $arrow('Bob'), "\n";
