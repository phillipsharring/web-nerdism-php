SET NAMES utf8mb4;

-- status is pending, confirmed or unsubscribed (checked in code). Only
-- confirmed addresses ever get the newsletter.
CREATE TABLE subscribers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(254) NOT NULL UNIQUE,
    status VARCHAR(12) NOT NULL DEFAULT 'pending',
    confirm_token_hash CHAR(64) NULL,     -- a hash: the token itself is only in the email
    confirm_sent_at DATETIME NULL,
    confirmed_at DATETIME NULL,
    unsubscribed_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- Every email the signup form sends, for the limits.
CREATE TABLE signup_mail (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(254) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX (ip, sent_at),
    INDEX (email, sent_at)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;
