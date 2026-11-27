# Good Variable Names

Lesson: https://phillipharrington.com/php/good-variable-names

`before.php` and `after.php` do the same thing. Only one of them says what.

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php before.php
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php after.php
```

`extract.php` is what not to do. Serve it and pick your own variables:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
curl http://localhost:8000/extract.php
curl 'http://localhost:8000/extract.php?isAdmin=1'
```
