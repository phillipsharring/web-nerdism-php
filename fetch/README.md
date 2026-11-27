# fetch() and AJAX

Lesson: https://phillipharrington.com/php/fetch

A search box that asks the server as you type. Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Open http://localhost:8000 and type "b". Then ask the server yourself:

```
curl 'http://localhost:8000/search.php?q=b'
curl -i "http://localhost:8000/search.php?q=$(printf 'x%.0s' {1..51})"
```
