SET NAMES utf8mb4;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    -- The collation compares without case, so Alice@ and alice@ are one address.
    email VARCHAR(254) NOT NULL UNIQUE,
    -- password_hash() output is 60 characters today; 255 leaves room for
    -- whatever PHP's default becomes.
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
