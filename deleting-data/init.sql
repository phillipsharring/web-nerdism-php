-- Users can close their accounts: deleted_at is NULL until they do.
CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(254) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `deleted_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
);

-- A user with posts can't be deleted outright: the default is RESTRICT.
CREATE TABLE `posts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `posts_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);

-- Delete a post, and its comments go with it.
CREATE TABLE `comments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `post_id` INT UNSIGNED NOT NULL,
    `body` VARCHAR(1000) NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `comments_post_id_fk` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
        ON DELETE CASCADE
);

INSERT INTO `users` (`email`, `name`)
VALUES
    ('alice@example.com', 'Alice'),
    ('bo@example.com', 'Bo');

INSERT INTO `posts` (`user_id`, `title`)
VALUES
    (1, 'Hello'),
    (1, 'A second post'),
    (2, 'Bo was here');

INSERT INTO `comments` (`post_id`, `body`)
VALUES
    (1, 'Welcome!'),
    (1, 'Nice to see you.'),
    (1, 'First!'),
    (3, 'Hi, Bo.');
