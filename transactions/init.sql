-- Two accounts, with balances in cents. A balance can't go below zero.
CREATE TABLE `accounts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `balance_cents` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`)
);

INSERT INTO `accounts` (`name`, `balance_cents`)
VALUES
    ('Alice', 10000),
    ('Bo', 5000);
