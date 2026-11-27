-- Runs once, the first time the database starts.
CREATE TABLE `members` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

-- Alice's password is "tigger", which is a bad one, on purpose.
INSERT INTO `members` (`email`, `password_hash`) VALUES
    ('alice@example.com', '$2y$12$ADRjmA/70IlGNqQWAye/Pegfb7djiwrxqt4S9XmdamSIZ9XF3vOoO');

-- One row per failed login, so the fixed page can count them.
CREATE TABLE `login_failures` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL,
    `ip` VARCHAR(45) NOT NULL,
    `failed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `by_email` (`email`, `failed_at`),
    INDEX `by_ip` (`ip`, `failed_at`)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
