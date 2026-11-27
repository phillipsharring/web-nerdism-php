# Coding Standards

Lesson: https://phillipharrington.com/php/coding-standards

`before.php` and `after.php` do the same thing. Only `after.php` follows PSR-12.

Install PHP_CodeSniffer:

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
```

Check each file, then fix a copy of the messy one:

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer check before.php
docker run --rm -v "$PWD":/app -w /app composer:2 composer check after.php
cp before.php fixed.php
docker run --rm -v "$PWD":/app -w /app composer:2 composer fix fixed.php
```

Both run the same:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php before.php
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php after.php
```
