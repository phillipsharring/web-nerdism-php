# JSON

Lesson: https://phillipharrington.com/php/json

PHP and JSON, both ways:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php types.php
```

An endpoint that takes a JSON order. Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Then, from a second terminal:

```
curl -i -H 'Content-Type: application/json' -d '{"size":"large","qty":2}' http://localhost:8000/order.php
curl -i -H 'Content-Type: application/json' -d '{"size":"large","qty":"2"}' http://localhost:8000/order.php
curl -i -H 'Content-Type: application/json' -d 'size=large' http://localhost:8000/order.php
```
