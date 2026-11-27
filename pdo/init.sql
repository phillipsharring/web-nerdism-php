-- Runs once, the first time the database starts.
SET NAMES utf8mb4;

CREATE TABLE books (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100) NOT NULL
) DEFAULT CHARSET = utf8mb4;

INSERT INTO books (title, author) VALUES
    ('Frankenstein', 'Mary Shelley'),
    ('Pride and Prejudice', 'Jane Austen'),
    ('Les Misérables', 'Victor Hugo'),
    ('Moby-Dick', 'Herman Melville');
