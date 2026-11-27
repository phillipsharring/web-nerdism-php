# Middleware and Pipelines

Lesson: https://phillipharrington.com/php/middleware

A request passes through three layers (`src/Middleware/`) before it reaches
`NotesHandler`, and the response passes back out through them.

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public public/index.php
```

Then:

```
curl -i http://localhost:8000/notes
curl -i -H 'Authorization: Bearer alice-token' http://localhost:8000/notes
curl -i -H 'Authorization: Bearer alice-token' http://localhost:8000/boom
```
