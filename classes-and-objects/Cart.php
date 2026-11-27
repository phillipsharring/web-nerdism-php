<?php
declare(strict_types=1);

// A class is a blueprint. This one describes a shopping cart: what each cart
// knows (its properties) and what it can do (its methods).
class Cart
{
    // Private: only the cart's own methods can read or change these.
    private array $items = [];
    private string $currency;

    // Runs once, when a cart is made with new Cart(...).
    public function __construct(string $currency)
    {
        $this->currency = $currency;
    }

    public function add(string $name, int $priceInCents, int $quantity = 1): void
    {
        if ($quantity < 1 || $priceInCents < 0) {
            throw new InvalidArgumentException('Quantity must be at least 1, and prices zero or more.');
        }
        $this->items[] = ['name' => $name, 'price' => $priceInCents, 'quantity' => $quantity];
    }

    public function count(): int
    {
        return array_sum(array_column($this->items, 'quantity'));
    }

    public function total(): int
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function formattedTotal(): string
    {
        $total = $this->total();
        return sprintf('%s %d.%02d', $this->currency, intdiv($total, 100), $total % 100);
    }
}
