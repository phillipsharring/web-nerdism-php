-- Authors, their books, and tags. Parents first: a table can only point at
-- a table that already exists.
CREATE TABLE `authors` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`id`)
);

-- Each book points at one author.
CREATE TABLE `books` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `author_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `year` SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `books_author_id_fk` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`)
);

CREATE TABLE `tags` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `tags_name_unique` (`name`)
);

-- A book has many tags, and a tag has many books: one row per pairing.
CREATE TABLE `book_tags` (
    `book_id` INT UNSIGNED NOT NULL,
    `tag_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`book_id`, `tag_id`),
    CONSTRAINT `book_tags_book_id_fk` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
    CONSTRAINT `book_tags_tag_id_fk` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`)
);

INSERT INTO `authors` (`name`)
VALUES
    ('Mary Shelley'),
    ('Octavia E. Butler'),
    ('Ursula K. Le Guin');

INSERT INTO `books` (`author_id`, `title`, `year`)
VALUES
    (1, 'Frankenstein', 1818),
    (2, 'Kindred', 1979),
    (2, 'Parable of the Sower', 1993),
    (3, 'A Wizard of Earthsea', 1968),
    (3, 'The Left Hand of Darkness', 1969);

INSERT INTO `tags` (`name`)
VALUES
    ('classic'),
    ('science fiction'),
    ('fantasy');

INSERT INTO `book_tags` (`book_id`, `tag_id`)
VALUES
    (1, 1),
    (1, 2),
    (2, 2),
    (3, 2),
    (4, 3),
    (5, 2);
