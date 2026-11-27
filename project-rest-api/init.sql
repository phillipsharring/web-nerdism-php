SET NAMES utf8mb4;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(254) NOT NULL UNIQUE
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- A token is a password for a program. Like a password, only its hash is
-- stored; the token itself is shown once, when it's made.
CREATE TABLE api_tokens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    can_write TINYINT(1) NOT NULL DEFAULT 0,
    last_used_at DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users (id)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE tasks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    done TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id),
    INDEX (user_id)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

INSERT INTO users (id, email) VALUES (1, 'ann@example.com'), (2, 'bo@example.com');

-- Example tokens, for trying it out (never put real ones in a file like this):
--   Ann, read and write:  wnp_ann_3f1c9a7e5b2d4f60
--   Ann, read only:       wnp_annro_8e2b7c4d1a9f3e56
--   Bo, read and write:   wnp_bo_6d4a2f9c1e7b3a58
INSERT INTO api_tokens (user_id, token_hash, name, can_write) VALUES
    (1, 'bc89597e81afe56baf1bb0ee509241ad1fa5aa7cab624f1de63f0a997c923419', 'Ann''s script', 1),
    (1, '2f41e9113a854adaf2d08b2e76a860306e7e96628663a5a446dc4cc156f1c885', 'Ann''s dashboard', 0),
    (2, '89b8eb5e55e371912488ebe5601391650f7483df3bf5df44033dfb69fce003a3', 'Bo''s phone', 1);

INSERT INTO tasks (user_id, title, done) VALUES
    (1, 'Write the API', 1),
    (1, 'Write the client', 0),
    (2, 'Bo''s private plans', 0);
