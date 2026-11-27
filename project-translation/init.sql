-- The file is UTF-8, and so is the connection that reads it.
SET NAMES utf8mb4;

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(20) NOT NULL UNIQUE
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- One row per product per language. A missing row falls back to English.
CREATE TABLE product_names (
    product_id INT UNSIGNED NOT NULL,
    lang VARCHAR(8) NOT NULL,
    name VARCHAR(100) NOT NULL,
    PRIMARY KEY (product_id, lang),
    FOREIGN KEY (product_id) REFERENCES products (id)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

INSERT INTO products (id, sku) VALUES (1, 'TEA'), (2, 'COFFEE'), (3, 'HONEY');

INSERT INTO product_names (product_id, lang, name) VALUES
    (1, 'en', 'Green tea'),  (1, 'fr', 'Thé vert'), (1, 'pl', 'Zielona herbata'), (1, 'ja', '緑茶'),
    (2, 'en', 'Coffee'),     (2, 'fr', 'Café'),     (2, 'pl', 'Kawa'),            (2, 'ja', 'コーヒー'),
    (3, 'en', 'Honey'),      (3, 'fr', 'Miel'),     (3, 'pl', 'Miód');
