# Defensive Programming

Lesson: https://phillipharrington.com/php/defensive-programming

The same function twice: `before/discount.php` trusts its caller,
`after/discount.php` checks. `run.php` calls one or the other:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php run.php before
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php run.php after
```

`shop.php` is the boundary, where input gets a polite answer:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
curl 'http://localhost:8000/shop.php?code=half'
curl -i 'http://localhost:8000/shop.php?code=FREE'
```
