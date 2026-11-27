<?php
declare(strict_types=1);

// The ways the list can be sorted, and the SQL for each. The visitor picks a
// key; only our own SQL ever reaches the query. (ORDER BY can't take a
// placeholder, which is exactly why this is an allow list.)
const SORTS = [
    'name' => 'p.name ASC',
    'price-low' => 'p.price_cents ASC, p.name ASC',
    'price-high' => 'p.price_cents DESC, p.name ASC',
];

/** @return list<array<string, mixed>> */
function categories(PDO $db): array
{
    return $db->query('SELECT slug, name FROM categories ORDER BY name')->fetchAll();
}

/** @return list<array<string, mixed>> active products, optionally in one category */
function products(PDO $db, ?string $category, string $sort): array
{
    $sql = 'SELECT p.slug, p.name, p.price_cents, p.stock FROM products p
              JOIN categories c ON c.id = p.category_id
             WHERE p.active = 1';
    $params = [];
    if ($category !== null) {
        $sql .= ' AND c.slug = ?';
        $params[] = $category;
    }
    $stmt = $db->prepare($sql . ' ORDER BY ' . SORTS[$sort]);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

/** @return array<string, mixed>|null */
function product(PDO $db, string $slug): ?array
{
    $stmt = $db->prepare(
        'SELECT p.sku, p.name, p.description, p.price_cents, p.stock, c.slug AS category, c.name AS category_name
           FROM products p JOIN categories c ON c.id = p.category_id
          WHERE p.slug = ? AND p.active = 1'
    );
    $stmt->execute([$slug]);

    return $stmt->fetch() ?: null;
}
