-- A small shop's products, with prices in cents and how many are in stock.
CREATE TABLE `products` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    `price_cents` INT UNSIGNED NOT NULL,
    `stock` INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
);

INSERT INTO `products` (`name`, `price_cents`, `stock`)
VALUES
    ('Mug', 1200, 3),
    ('T-shirt', 2000, 1),
    ('Poster', 1500, 0);
