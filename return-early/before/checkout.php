<?php

declare(strict_types=1);

const MAX_ITEMS = 20;

// Every check wraps the next one. The answer to each is a long way below it.
function checkoutMessage(?array $user, array $items): string
{
    if ($user !== null) {
        if ($user['verified']) {
            if ($items !== []) {
                if (count($items) <= MAX_ITEMS) {
                    return 'Ready to check out ' . count($items) . ' items.';
                } else {
                    return 'Too many items: ' . MAX_ITEMS . ' at most.';
                }
            } else {
                return 'Your cart is empty.';
            }
        } else {
            return 'Please verify your email first.';
        }
    } else {
        return 'Please log in.';
    }
}
