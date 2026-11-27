# Operators

Lesson: https://phillipharrington.com/php/operators

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php math.php
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php compare.php
```

Then the one-time code that `==` gets wrong:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

```
curl 'http://localhost:8000/code.php?code=123456'
curl 'http://localhost:8000/code.php?code=0'
```
