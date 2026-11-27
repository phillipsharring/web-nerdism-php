<?php

declare(strict_types=1);

const MAX_ITEMS = 20;

// Each check, then its answer, then out. What's left at the bottom is the real work.
function checkoutMessage(?array $user, array $items): string
{
    if ($user === null) {
        return 'Please log in.';
    }
    if (!$user['verified']) {
        return 'Please verify your email first.';
    }
    if ($items === []) {
        return 'Your cart is empty.';
    }
    if (count($items) > MAX_ITEMS) {
        return 'Too many items: ' . MAX_ITEMS . ' at most.';
    }

    return 'Ready to check out ' . count($items) . ' items.';
}
