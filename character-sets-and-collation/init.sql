-- Two tables for notes. The first says utf8, as a lot of older tables do.
-- In MySQL, utf8 means utf8mb3: at most three bytes per character.
CREATE TABLE `notes_utf8` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `body` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

-- The second says utf8mb4: real UTF-8, up to four bytes, emoji included.
CREATE TABLE `notes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `body` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Usernames must be unique. Unique according to the collation, that is.
CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_username_unique` (`username`)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `words` (
    `word` VARCHAR(50) NOT NULL
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `words` (`word`)
VALUES
    ('zebra'),
    ('Émile'),
    ('apple'),
    ('Zoë'),
    ('éclair');
