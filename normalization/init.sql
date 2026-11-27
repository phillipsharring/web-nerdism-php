-- The flat way: one table, and the customer's details on every order.
CREATE TABLE `orders_flat` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `customer_name` VARCHAR(200) NOT NULL,
    `customer_email` VARCHAR(254) NOT NULL,
    `product` VARCHAR(200) NOT NULL,
    `price_cents` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`)
);

INSERT INTO `orders_flat` (`customer_name`, `customer_email`, `product`, `price_cents`)
VALUES
    ('Alice Rivera', 'alice@example.com', 'Mug', 1200),
    ('Bo Chen', 'bo@example.com', 'T-shirt', 2000),
    ('Alice Rivera', 'alice@example.com', 'T-shirt', 2000),
    ('Alice Rivera', 'alice@example.com', 'Poster', 1500);

-- The normalized way: each fact once, in the table it's about.
CREATE TABLE `customers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    `email` VARCHAR(254) NOT NULL,
    -- A copy, on purpose: always equal to this customer's number of orders.
    -- place-order.php keeps it up to date. To rebuild it:
    -- UPDATE `customers` SET `order_count` = (SELECT COUNT(*) FROM `orders` WHERE `orders`.`customer_id` = `customers`.`id`);
    `order_count` INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
);

CREATE TABLE `products` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    `price_cents` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`)
);

-- price_cents here is what this customer paid, which is its own fact:
-- it stays the same when the product's price changes.
CREATE TABLE `orders` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `customer_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `price_cents` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `orders_customer_id_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
    CONSTRAINT `orders_product_id_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
);

INSERT INTO `customers` (`name`, `email`, `order_count`)
VALUES
    ('Alice Rivera', 'alice@example.com', 3),
    ('Bo Chen', 'bo@example.com', 1);

INSERT INTO `products` (`name`, `price_cents`)
VALUES
    ('Mug', 1200),
    ('T-shirt', 2000),
    ('Poster', 1500);

INSERT INTO `orders` (`customer_id`, `product_id`, `price_cents`)
VALUES
    (1, 1, 1200),
    (2, 2, 2000),
    (1, 2, 2000),
    (1, 3, 1500);
