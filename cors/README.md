# CORS

Lesson: https://phillipharrington.com/php/cors

Two sites on two origins: an API on port 8000, and a page on port 8001 that
calls it. Start each in its own terminal:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t api
docker run --rm -it -p 8001:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t site
```

Open http://localhost:8001. Then take `http://localhost:8001` off the allow
list in `api/index.php` and reload. And ask the way a browser does, first:

```
curl -i -X OPTIONS -H 'Origin: http://localhost:8001' -H 'Access-Control-Request-Method: POST' http://localhost:8000/
curl -i -H 'Origin: http://evil.example' http://localhost:8000/
```
