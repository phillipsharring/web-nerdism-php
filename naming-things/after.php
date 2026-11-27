<?php
declare(strict_types=1);

// The same code as before.php, line for line. Only the names changed, and one
// ! went away with the negative name that needed it.

const FREE_SHIPPING_MINIMUM_CENTS = 5000;

class FreeShippingPolicy
{
    private array $orders;

    public function __construct(array $orders)
    {
        $this->orders = $orders;
    }

    public function qualifyingOrderIds(): array
    {
        $orderIds = [];
        foreach ($this->orders as $order) {
            if ($this->qualifies($order)) {
                $orderIds[] = $order['id'];
            }
        }
        return $orderIds;
    }

    private function qualifies(array $order): bool
    {
        $isPaidAndDomestic = $order['isPaid'] && $order['isDomestic'];
        return $isPaidAndDomestic && $order['totalCents'] >= FREE_SHIPPING_MINIMUM_CENTS;
    }
}

$policy = new FreeShippingPolicy([
    ['id' => 'A-1001', 'isPaid' => true, 'isDomestic' => true, 'totalCents' => 6400],
    ['id' => 'A-1002', 'isPaid' => false, 'isDomestic' => true, 'totalCents' => 9900],
    ['id' => 'A-1003', 'isPaid' => true, 'isDomestic' => true, 'totalCents' => 5000],
    ['id' => 'A-1004', 'isPaid' => true, 'isDomestic' => false, 'totalCents' => 12000],
    ['id' => 'A-1005', 'isPaid' => true, 'isDomestic' => true, 'totalCents' => 1800],
]);

echo 'Free shipping: ' . implode(', ', $policy->qualifyingOrderIds()) . "\n";
