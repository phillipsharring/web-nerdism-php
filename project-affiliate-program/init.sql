SET NAMES utf8mb4;

-- commission_bps is the cut in basis points, hundredths of a percent:
-- 1000 is 10%. Whole numbers, like the money.
CREATE TABLE affiliates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(254) NOT NULL,
    commission_bps INT UNSIGNED NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE clicks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    affiliate_id INT UNSIGNED NOT NULL,
    landed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (affiliate_id) REFERENCES affiliates (id)
);

CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(254) NOT NULL,
    total_cents INT UNSIGNED NOT NULL,
    affiliate_id INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (affiliate_id) REFERENCES affiliates (id)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- One commission per order, at most. status is pending, approved or
-- reversed (checked in code): pending until the refund window has passed.
CREATE TABLE commissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    affiliate_id INT UNSIGNED NOT NULL,
    order_id INT UNSIGNED NOT NULL UNIQUE,
    amount_cents INT UNSIGNED NOT NULL,
    status VARCHAR(10) NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (affiliate_id) REFERENCES affiliates (id),
    FOREIGN KEY (order_id) REFERENCES orders (id)
);

INSERT INTO affiliates (code, name, email, commission_bps, active) VALUES
    ('ALICE10', 'Alice''s Blog', 'alice@example.com', 1000, 1),
    ('BOBREVIEWS', 'Bob Reviews', 'bob@example.com', 750, 1),
    ('OLDPARTNER', 'Old Partner', 'old@example.com', 1500, 0);
