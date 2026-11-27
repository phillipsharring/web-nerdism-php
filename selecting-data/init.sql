-- A reading list: authors, and their books, with your rating.
-- A rating of NULL means you haven't rated it yet.
CREATE TABLE `authors` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `books` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `author_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `year` SMALLINT UNSIGNED NOT NULL,
    `rating` TINYINT UNSIGNED NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `books_author_id_fk` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`)
);

INSERT INTO `authors` (`name`)
VALUES
    ('Mary Shelley'),
    ('Octavia E. Butler'),
    ('Ursula K. Le Guin'),
    ('Toni Morrison');

-- Toni Morrison is on the list, with no books yet.
INSERT INTO `books` (`author_id`, `title`, `year`, `rating`)
VALUES
    (1, 'Frankenstein', 1818, 5),
    (2, 'Kindred', 1979, 5),
    (2, 'Parable of the Sower', 1993, NULL),
    (3, 'A Wizard of Earthsea', 1968, 4),
    (3, 'The Left Hand of Darkness', 1969, NULL),
    (3, 'The Dispossessed', 1974, 3);
