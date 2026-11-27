# Separating Concerns

Lesson: https://phillipharrington.com/php/separating-concerns

The same page twice. `before/index.php` reads, decides and shows all at once.
`after/` splits it: `src/data.php` reads, `src/rules.php` decides,
`views/posts.php` shows, and `public/index.php` joins them up.

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t before
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t after/public
```

Then check the rules on their own, with no server:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php after/check.php
```
