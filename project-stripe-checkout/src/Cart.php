<?php
declare(strict_types=1);

namespace Shop;

/**
 * What someone means to buy: product ids and how many of each. Nothing else.
 * No names, no prices. Those come from the database every time, so the cart
 * can never hold a price someone made up.
 */
final class Cart
{
    public const MAX_QUANTITY = 99;
    public const MAX_LINES = 50;

    /** @var array<int, int> product id => quantity */
    private array $items = [];

    /**
     * Rebuilds a cart from what was saved in the session. Anything that isn't
     * a sensible id and quantity is dropped.
     */
    public function __construct(mixed $saved = [])
    {
        foreach (is_array($saved) ? $saved : [] as $id => $quantity) {
            if (is_int($id) && is_int($quantity)) {
                $this->set($id, $quantity);
            }
        }
    }

    public function add(int $productId, int $quantity = 1): void
    {
        $this->set($productId, ($this->items[$productId] ?? 0) + $quantity);
    }

    /** Sets a line's quantity. Zero or less removes it; too many is capped. */
    public function set(int $productId, int $quantity): void
    {
        if ($productId < 1) {
            return;
        }
        if ($quantity < 1) {
            unset($this->items[$productId]);
            return;
        }
        if (!isset($this->items[$productId]) && count($this->items) >= self::MAX_LINES) {
            return;
        }
        $this->items[$productId] = min($quantity, self::MAX_QUANTITY);
    }

    public function remove(int $productId): void
    {
        unset($this->items[$productId]);
    }

    /** @return array<int, int> */
    public function items(): array
    {
        return $this->items;
    }

    /** How many things in all, for the "Cart (3)" link. */
    public function count(): int
    {
        return array_sum($this->items);
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }
}
