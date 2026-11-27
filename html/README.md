# HTML

Lesson: https://phillipharrington.com/php/html

A whole HTML page, built by PHP. Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

Open http://localhost:8000 and view the source. Then send a name with HTML in it:

```
curl 'http://localhost:8000/?name=%3Cem%3EAlice%3C/em%3E'
```
