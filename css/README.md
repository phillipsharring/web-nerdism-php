# CSS

Lesson: https://phillipharrington.com/php/css

A page, its stylesheet, and a theme picked from an allow list. Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Open http://localhost:8000 and switch themes. Then try a theme that isn't one:

```
curl 'http://localhost:8000/?theme=%22%3E%3Cscript%3E'
```
