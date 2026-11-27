# Magic Numbers in Code

Lesson: https://phillipharrington.com/php/magic-numbers

`before.php` is full of numbers that mean something only to whoever wrote them.
`after.php` names every one, in `rules.php`.

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php before.php
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php after.php
```
