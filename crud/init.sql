-- Runs once, the first time the database starts. Without SET NAMES, the café below comes out garbled.
SET NAMES utf8mb4;

CREATE TABLE notes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    body VARCHAR(500) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

INSERT INTO notes (body) VALUES
    ('Order more oat milk'),
    ('Fix the café sign');
