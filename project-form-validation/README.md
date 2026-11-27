# Project: Form Validation

Project: https://phillipharrington.com/php/projects/form-validation

A reusable `Validator` class (`src/Validator.php`), a registration form that
uses it (`public/index.php`), and PHPUnit tests for it
(`tests/ValidatorTest.php`).

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php vendor/bin/phpunit
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Open http://localhost:8000 and press Register with nothing filled in. Then:

```
curl -d 'name=Alice&email=alice@&age=12abc&ticket=free' http://localhost:8000
```
