SET NAMES utf8mb4;

CREATE TABLE posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    body TEXT NOT NULL
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE comments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id INT UNSIGNED NOT NULL,
    name VARCHAR(80) NOT NULL,
    body TEXT NOT NULL,
    ip VARCHAR(45) NOT NULL,                -- long enough for IPv6
    approved TINYINT(1) NOT NULL DEFAULT 0, -- 0 waits for a person to look at it
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts (id),
    INDEX (post_id, approved),
    INDEX (ip, created_at)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

INSERT INTO posts (id, title, body) VALUES (1, 'Hello', 'Comments are open. Be nice.');
