-- Runs once, the first time the database starts.
SET NAMES utf8mb4;

CREATE TABLE notes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    body VARCHAR(500) NOT NULL,
    created_at DATETIME NOT NULL
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
