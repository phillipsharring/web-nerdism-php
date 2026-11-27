# Arrays

Lesson: https://phillipharrington.com/php/arrays

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php arrays.php
```

Then see the arrays a query string can make:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

```
curl 'http://localhost:8000/query.php?tag=php'
curl -g 'http://localhost:8000/query.php?tag[]=php&tag[]=web&size[shirt]=large'
```
