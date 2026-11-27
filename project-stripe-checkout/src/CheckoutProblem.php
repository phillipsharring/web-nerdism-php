<?php
declare(strict_types=1);

namespace Shop;

use RuntimeException;

/** A problem the customer can fix, with a message meant for them. */
final class CheckoutProblem extends RuntimeException
{
}
