# Dependency Injection

Lesson: https://phillipharrington.com/php/dependency-injection

Install (PHP-DI, the container), then wire it by hand, then with the container:

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php by-hand.php
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php container.php
```
