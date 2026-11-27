# Documentation: php.net

Lesson: https://phillipharrington.com/php/documentation

Ask PHP for a function's signature, then run the example that ignores the
manual's warning:

```
docker run --rm php:8.4-cli php --rf strpos
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php strpos.php
```

The manual's page for it: https://www.php.net/strpos
