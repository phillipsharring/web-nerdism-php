SET NAMES utf8mb4;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(254) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(50) NOT NULL DEFAULT '',
    bio VARCHAR(500) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE login_failures (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(254) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX (email, failed_at),
    INDEX (ip, failed_at)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- One member, on your own computer only. Password: correct horse battery staple
INSERT INTO users (email, password_hash, display_name, bio) VALUES
    ('alice@example.com', '$2y$12$noC7DVSxqpYFmE7NBKN48O9sDfYQ9/T8LfV0RjNOgF34NYMDqvv1q', 'Alice', 'I like long walks and short queries.');
