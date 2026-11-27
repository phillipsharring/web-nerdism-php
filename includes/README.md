# Includes

Lesson: https://phillipharrington.com/php/includes

A small site from pieces. Serve `public/`:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Open http://localhost:8000 and click between the pages. Then try to make it
include something else:

```
curl -i 'http://localhost:8000/?page=../config'
```

And the difference between include and require:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php missing.php
```
