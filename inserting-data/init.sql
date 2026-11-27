-- A newsletter's subscribers. Each email address can sign up once.
CREATE TABLE `subscribers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(254) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `subscribers_email_unique` (`email`)
);
