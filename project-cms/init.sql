SET NAMES utf8mb4;

CREATE TABLE pages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(60) NOT NULL UNIQUE,
    title VARCHAR(200) NOT NULL,
    body TEXT NOT NULL,
    image VARCHAR(64) NULL,          -- a stored file name we made up, never the uploader's
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

INSERT INTO pages (slug, title, body) VALUES
    ('home', 'Welcome', 'This page comes from the database.\n\nEdit it at /admin.php.'),
    ('about', 'About us', 'We make small, sturdy websites.');
