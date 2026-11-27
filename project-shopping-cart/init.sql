SET NAMES utf8mb4;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- Prices are whole cents in an integer: never a float. $12.50 is 1250.
CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    sku VARCHAR(30) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    price_cents INT UNSIGNED NOT NULL,
    stock INT UNSIGNED NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,      -- 0 hides it without deleting it
    FOREIGN KEY (category_id) REFERENCES categories (id),
    INDEX (active, category_id)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

INSERT INTO categories (id, slug, name) VALUES (1, 'mugs', 'Mugs'), (2, 'shirts', 'Shirts'), (3, 'stickers', 'Stickers');

INSERT INTO products (category_id, sku, slug, name, description, price_cents, stock, active) VALUES
    (1, 'MUG-BLK', 'black-mug', 'Black mug', 'Holds 350 ml of whatever keeps you going.', 1400, 25, 1),
    (1, 'MUG-WHT', 'white-mug', 'White mug', 'The same mug, in white.', 1400, 0, 1),
    (1, 'MUG-XL', 'big-mug', 'Big mug', 'For long nights. 600 ml.', 1850, 8, 1),
    (2, 'TEE-S', 'shirt-small', 'Shirt, small', 'Soft cotton, printed on the front.', 2200, 12, 1),
    (2, 'TEE-L', 'shirt-large', 'Shirt, large', 'Soft cotton, printed on the front.', 2200, 5, 1),
    (3, 'STK-3', 'sticker-pack', 'Sticker pack', 'Three stickers for your laptop. Café-proof.', 499, 100, 1),
    (3, 'STK-OLD', 'old-sticker', 'Old sticker', 'No longer sold.', 199, 40, 0);
