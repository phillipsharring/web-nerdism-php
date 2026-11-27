# Testing

Lesson: https://phillipharrington.com/php/testing

The verification link from Two-Dollar Test Harnesses and the welcome email
from Dependency Injection, with PHPUnit tests. Install, then run the tests:

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker run --rm -v "$PWD":/app -w /app php:8.4-cli vendor/bin/phpunit
```

Run one test class, or one test, by name:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli vendor/bin/phpunit --filter Expired
```

Break the expiry check in `src/verification.php` and run them again to watch
one fail.
