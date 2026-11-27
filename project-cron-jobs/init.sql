SET NAMES utf8mb4;

-- Every failed login, kept for the rate limit. After a while they're no use
-- to anyone, and a table that only grows gets slower to search.
CREATE TABLE login_attempts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(254) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    attempted_at DATETIME NOT NULL,
    INDEX (attempted_at)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- 2,500 old attempts and 5 recent ones. (MySQL stops a recursive query at
-- 1,000 rounds unless told otherwise.)
SET SESSION cte_max_recursion_depth = 2500;
INSERT INTO login_attempts (email, ip, attempted_at)
WITH RECURSIVE n (i) AS (SELECT 1 UNION ALL SELECT i + 1 FROM n WHERE i < 2500)
SELECT CONCAT('user', i, '@example.com'), '203.0.113.7', UTC_TIMESTAMP() - INTERVAL (31 + i % 300) DAY FROM n;

INSERT INTO login_attempts (email, ip, attempted_at) VALUES
    ('a@example.com', '203.0.113.8', UTC_TIMESTAMP() - INTERVAL 1 DAY),
    ('b@example.com', '203.0.113.8', UTC_TIMESTAMP() - INTERVAL 2 DAY),
    ('c@example.com', '203.0.113.9', UTC_TIMESTAMP() - INTERVAL 3 DAY),
    ('d@example.com', '203.0.113.9', UTC_TIMESTAMP() - INTERVAL 10 DAY),
    ('e@example.com', '203.0.113.9', UTC_TIMESTAMP() - INTERVAL 29 DAY);
