-- One table, with a few books in it. Creating Tables, next, explains this part.
CREATE TABLE `books` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `author` VARCHAR(200) NOT NULL,
    `year` SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`)
);

INSERT INTO `books` (`title`, `author`, `year`)
VALUES
    ('Pride and Prejudice', 'Jane Austen', 1813),
    ('Frankenstein', 'Mary Shelley', 1818),
    ('Jane Eyre', 'Charlotte Brontë', 1847),
    ('Moby-Dick', 'Herman Melville', 1851),
    ('Dracula', 'Bram Stoker', 1897),
    ('The Left Hand of Darkness', 'Ursula K. Le Guin', 1969),
    ('Kindred', 'Octavia E. Butler', 1979);
