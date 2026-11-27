# Your Dependencies Are Your Code Too

Lesson: https://phillipharrington.com/php/dependencies

The Try It installs an old Guzzle (7.4.0) on purpose, to see `composer audit`
find its holes, then updates it. Nothing vulnerable is committed here: you
make it yourself.

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer require guzzlehttp/guzzle:7.4.0
docker run --rm -e COMPOSER_NO_SECURITY_BLOCKING=1 -v "$PWD":/app -w /app composer:2 composer require guzzlehttp/guzzle:7.4.0
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php version.php
docker run --rm -v "$PWD":/app -w /app composer:2 composer audit
docker run --rm -v "$PWD":/app -w /app composer:2 composer require "guzzlehttp/guzzle:^7.4" --with-all-dependencies
```

The first `require` is refused (that's the point). Never set
`COMPOSER_NO_SECURITY_BLOCKING` in a real project.
