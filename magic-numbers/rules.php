<?php

declare(strict_types=1);

// This file only declares things. after.php is the one that does things.

namespace Shop;

enum OrderStatus: int
{
    case Pending = 1;
    case Paid = 2;
    case Shipped = 3;
}

const FREE_SHIPPING_FROM_CENTS = 5000;
const SHIPPING_CENTS = 599;
const SALES_TAX_RATE = 0.0725;
const RETURN_WINDOW_DAYS = 30;
