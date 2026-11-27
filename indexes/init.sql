-- A table with no index but its primary key, and 200,000 made-up customers.
CREATE TABLE `customers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(254) NOT NULL,
    `name` VARCHAR(200) NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
);

-- A recursive query counts from 1 to 200,000, and each number becomes a row.
SET SESSION cte_max_recursion_depth = 200000;

INSERT INTO `customers` (`email`, `name`, `city`, `created_at`)
WITH RECURSIVE `numbers` (`n`) AS (
    SELECT 1
    UNION ALL
    SELECT `n` + 1
    FROM `numbers`
    WHERE `n` < 200000
)
SELECT
    CONCAT('user', `n`, '@example.com'),
    CONCAT('Customer ', `n`),
    ELT(1 + `n` % 5, 'Portland', 'Austin', 'Denver', 'Boston', 'Chicago'),
    '2026-01-01' + INTERVAL `n` MINUTE
FROM `numbers`;

-- Refresh the table's statistics, so EXPLAIN's row estimates are right.
ANALYZE TABLE `customers`;
