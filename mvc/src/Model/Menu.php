<?php

declare(strict_types=1);

namespace App\Model;

// The model: the data and the rules about it. No HTML, no $_GET.
final class Menu
{
    /** @param MenuItem[] $items */
    public function __construct(private array $items)
    {
    }

    /** @return string[] */
    public function categories(): array
    {
        return array_values(array_unique(array_map(fn(MenuItem $item) => $item->category, $this->items)));
    }

    /** @return MenuItem[] */
    public function inCategory(?string $category): array
    {
        if ($category === null) {
            return $this->items;
        }
        return array_values(array_filter($this->items, fn(MenuItem $item) => $item->category === $category));
    }

    public function find(int $id): ?MenuItem
    {
        foreach ($this->items as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }
        return null;
    }
}
