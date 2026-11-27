-- The books table, column by column. MySQL runs this once, on an empty database.
CREATE TABLE `books` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `format` VARCHAR(20) NOT NULL,
    `pages` SMALLINT UNSIGNED NULL,
    `price_cents` INT UNSIGNED NOT NULL,
    `published_on` DATE NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
