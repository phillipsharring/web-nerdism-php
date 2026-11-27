# REST

Lesson: https://phillipharrington.com/php/rest

A JSON API for notes, with one front door, `public/index.php`. The notes live in
a file in the container's temp folder, so they reset when it stops.

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public public/index.php
```

Then:

```
curl -i http://localhost:8000/notes
curl -i -H 'Content-Type: application/json' -d '{"body":"Buy beans"}' http://localhost:8000/notes
curl -i http://localhost:8000/notes/3
curl -i -X PUT -H 'Content-Type: application/json' -d '{"body":"Buy more beans"}' http://localhost:8000/notes/3
curl -i -X DELETE http://localhost:8000/notes/3
```
