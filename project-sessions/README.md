# Project: Sessions

Project: https://phillipharrington.com/php/projects/sessions

A shortlist kept in a session: add and remove items, a message shown once
after each change, and a Start over button that ends the session. The
session is started with strict mode and cookie flags in `src/session.php`.

```
docker run --rm -it --name sessions -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Open http://localhost:8000 and add a few things. Then look at the file the
server keeps, from a second terminal:

```
docker exec sessions sh -c 'cat /tmp/sess_*'
```
