# Single Point of Entry

Lesson: https://phillipharrington.com/php/single-point-of-entry

`before/` is a page per file, each remembering its own setup; `admin.php`
forgot its login check. `after/` sends every request through
`public/index.php`, which checks once for every page.

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t before/public
curl http://localhost:8000/admin.php
```

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t after/public after/public/index.php
curl -i http://localhost:8000/admin
curl -i -H 'Authorization: Bearer alice-token' http://localhost:8000/admin
curl -i -H 'Authorization: Bearer root-token' http://localhost:8000/admin
```
