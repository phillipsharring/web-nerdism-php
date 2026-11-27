<?php
declare(strict_types=1);

namespace Shop;

use PDO;

/** Turns a cart into lines with names and prices, from the database. */
final class Pricing
{
    public function __construct(private PDO $db)
    {
    }

    /**
     * Each line with today's price. Products that are gone are left out, and
     * quantities are cut to what's in stock.
     *
     * @return array{lines: list<array{id: int, name: string, slug: string, unit_cents: int, quantity: int, line_cents: int, limited: bool}>, total_cents: int}
     */
    public function price(Cart $cart): array
    {
        $items = $cart->items();
        if ($items === []) {
            return ['lines' => [], 'total_cents' => 0];
        }

        $marks = implode(',', array_fill(0, count($items), '?'));
        $stmt = $this->db->prepare(
            "SELECT id, name, slug, price_cents, stock FROM products WHERE active = 1 AND id IN ($marks) ORDER BY name"
        );
        $stmt->execute(array_keys($items));

        $lines = [];
        $total = 0;
        foreach ($stmt->fetchAll() as $p) {
            $wanted = $items[(int) $p['id']];
            $quantity = min($wanted, (int) $p['stock']);
            if ($quantity < 1) {
                continue;
            }
            $line = $quantity * (int) $p['price_cents'];
            $lines[] = [
                'id' => (int) $p['id'],
                'name' => $p['name'],
                'slug' => $p['slug'],
                'unit_cents' => (int) $p['price_cents'],
                'quantity' => $quantity,
                'line_cents' => $line,
                'limited' => $quantity < $wanted,
            ];
            $total += $line;
        }

        return ['lines' => $lines, 'total_cents' => $total];
    }
}
