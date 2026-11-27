-- Runs once, the first time the database starts.
CREATE TABLE `members` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

INSERT INTO `members` (`email`, `name`, `password_hash`) VALUES
    ('alice@example.com', 'Alice', '$2y$12$HEA59rdH50QDhKzo1h3G7Oxt0X.q59l7rP5Jio4u/kbNJ2Xd3TO4i'),
    ('bob@example.com', 'Bob', '$2y$12$Tj1fZosmNkQQ1LO6zcMr9Or2WaIl8AfptGAbNefTZTTuIPPWS0ULa'),
    ('chloe@example.com', 'Chloé', '$2y$12$eVGWV53tJlybT6QhQDs45ebkfpm33TCNZzTT4i0uJ.FR1V4A6lHey');
