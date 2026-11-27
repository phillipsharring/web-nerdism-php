SET NAMES utf8mb4;

CREATE TABLE books (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100) NOT NULL,
    year SMALLINT NOT NULL
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

INSERT INTO books (title, author, year) VALUES
    ('Pride and Prejudice', 'Jane Austen', 1813),
    ('Frankenstein', 'Mary Shelley', 1818),
    ('Moby-Dick', 'Herman Melville', 1851),
    ('Les Misérables', 'Victor Hugo', 1862),
    ('Little Women', 'Louisa May Alcott', 1868),
    ('Middlemarch', 'George Eliot', 1871),
    ('The Time Machine', 'H. G. Wells', 1895),
    ('Dracula', 'Bram Stoker', 1897);

-- Another table in the same database. The search page never asks for it.
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(254) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
) DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

INSERT INTO users (email, password_hash) VALUES
    ('admin@example.com', '$2y$12$V7YmYyHThlzBFxJ50/IaK.NxdbvumRLq0B6SP9OoE1Zzm8.NxxwmS'),
    ('librarian@example.com', '$2y$12$pXjePItb5XPrLW28lDK/tuRc/2Xpp/fA7bfvOj.qZhL05PItyzNSG');
