<?php
declare(strict_types=1);

namespace Shop;

use PDO;

/** Turns a cart into an order, once. */
final class Orders
{
    public function __construct(private PDO $db, private Pricing $pricing)
    {
    }

    /**
     * Places the order and returns its public id. Prices come from the
     * database, stock is checked and taken under a lock, and the same
     * checkout token always gives back the same order.
     */
    public function place(Cart $cart, string $email, string $name, string $checkoutToken): string
    {
        $existing = $this->findByToken($checkoutToken);
        if ($existing !== null) {
            return $existing; // pressed twice, or reloaded: still one order
        }

        $this->db->beginTransaction();
        try {
            // Lock the products' rows, so two checkouts can't both take the last mug.
            $ids = array_keys($cart->items());
            if ($ids === []) {
                throw new CheckoutProblem('Your cart is empty.');
            }
            $marks = implode(',', array_fill(0, count($ids), '?'));
            $this->db->prepare("SELECT id FROM products WHERE id IN ($marks) FOR UPDATE")->execute($ids);

            $priced = $this->pricing->price($cart);
            foreach ($priced['lines'] as $line) {
                if ($line['limited']) {
                    throw new CheckoutProblem("Only {$line['quantity']} {$line['name']} left. Check your cart.");
                }
            }
            if ($priced['lines'] === [] || count($priced['lines']) !== count($ids)) {
                throw new CheckoutProblem('Something in your cart is no longer available. Check your cart.');
            }

            $publicId = bin2hex(random_bytes(16));
            $this->db->prepare(
                'INSERT INTO orders (public_id, checkout_token, email, name, total_cents) VALUES (?, ?, ?, ?, ?)'
            )->execute([$publicId, $checkoutToken, $email, $name, $priced['total_cents']]);
            $orderId = (int) $this->db->lastInsertId();

            $item = $this->db->prepare(
                'INSERT INTO order_items (order_id, product_id, name, unit_cents, quantity, line_cents) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stock = $this->db->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');
            foreach ($priced['lines'] as $line) {
                $item->execute([$orderId, $line['id'], $line['name'], $line['unit_cents'], $line['quantity'], $line['line_cents']]);
                $stock->execute([$line['quantity'], $line['id']]);
            }

            $this->db->commit();

            return $publicId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** @return array<string, mixed>|null the order and its items */
    public function find(string $publicId): ?array
    {
        $stmt = $this->db->prepare('SELECT id, public_id, email, name, status, total_cents FROM orders WHERE public_id = ?');
        $stmt->execute([$publicId]);
        $order = $stmt->fetch();
        if ($order === false) {
            return null;
        }
        $items = $this->db->prepare('SELECT name, unit_cents, quantity, line_cents FROM order_items WHERE order_id = ? ORDER BY name');
        $items->execute([$order['id']]);
        $order['items'] = $items->fetchAll();

        return $order;
    }

    private function findByToken(string $token): ?string
    {
        $stmt = $this->db->prepare('SELECT public_id FROM orders WHERE checkout_token = ?');
        $stmt->execute([$token]);

        return $stmt->fetchColumn() ?: null;
    }
}
