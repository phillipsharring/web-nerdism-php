# Return Early

Lesson: https://phillipharrington.com/php/return-early

The same function twice: nested in `before/`, returning early in `after/`.
`run.php` asks both the same five questions:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php run.php before
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php run.php after
```
