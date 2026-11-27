# HTMX

Lesson: https://phillipharrington.com/php/htmx

A search box that updates as you type, with no JavaScript of your own. Start
the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Open http://localhost:8000 and type "b". Then ask the way htmx does, and the
way a browser without JavaScript does:

```
curl -H 'HX-Request: true' 'http://localhost:8000/?q=b'
curl 'http://localhost:8000/?q=b'
```
