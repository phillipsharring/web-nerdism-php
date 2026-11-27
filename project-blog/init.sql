SET NAMES utf8mb4;

-- published_at is in UTC. NULL means a draft; a time in the future means
-- scheduled. Either way, nobody sees it until the time comes.
CREATE TABLE posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(200) NOT NULL,
    body TEXT NOT NULL,
    published_at DATETIME NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (published_at)
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

INSERT INTO posts (slug, title, body, published_at) VALUES
    ('hello', 'Hello', 'The first post.\n\nIt has two paragraphs.', '2026-11-01 09:00:00'),
    ('tables', 'On tables', 'Say each fact once.', '2026-11-05 09:00:00'),
    ('indexes', 'On indexes', 'An index is a sorted copy of a column.', '2026-11-10 09:00:00'),
    ('cafe', 'Café notes', 'Accents survive the trip: café, naïve, Zoë.', '2026-11-15 09:00:00'),
    ('escaping', 'Escaping <b>output</b>', 'A title with HTML in it shows up as text.', '2026-11-20 09:00:00'),
    ('unfinished', 'Not ready yet', 'A draft.', NULL),
    ('someday', 'From the future', 'Scheduled.', '2099-01-01 09:00:00');
