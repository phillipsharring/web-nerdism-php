<?php
declare(strict_types=1);

/**
 * 1250 cents as "$12.50". Integer math only: intdiv() for the dollars, % for
 * the cents, so no float ever rounds a price.
 */
function money(int $cents): string
{
    return '$' . number_format(intdiv($cents, 100)) . '.' . str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
}
