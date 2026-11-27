# Autoloading with Composer and PSR-4

Lesson: https://phillipharrington.com/php/autoloading

First, an autoloader written by hand:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php by-hand.php
```

Then let Composer write it. Install (this makes `vendor/`), then run:

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php hello.php
```

Or through the script in `composer.json`:

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer run hello
```
