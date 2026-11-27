# Single Point of Return

Lesson: https://phillipharrington.com/php/single-point-of-return

The same shipping quote twice. `before/` returns from wherever it knows the
answer, and forgets to log two of them. `after/` returns early from its guards,
then builds one result and returns it once, logged.

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php run.php before
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php run.php after
```
