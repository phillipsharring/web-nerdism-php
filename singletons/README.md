# Singletons

Lesson: https://phillipharrington.com/php/singletons

`before/` uses a singleton mailer. Its two tests run the same code with the
same expectation, and the second fails, because the first one's email is still
there. `after/` passes the mailer in, so each test gets its own.

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php before/tests.php
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php after/tests.php
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php after/app.php
```
