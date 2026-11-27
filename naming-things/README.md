# Naming Things

Lesson: https://phillipharrington.com/php/naming-things

`before.php` and `after.php` are the same code with different names. Both
print the same thing; only one says what it's doing.

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php before.php
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php after.php
```
