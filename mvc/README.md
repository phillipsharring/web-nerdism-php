# MVC

Lesson: https://phillipharrington.com/php/mvc

A café menu in three parts: the model (`src/Model/`), the views (`views/`) and
the controller (`src/Controller/`). `public/index.php` and `public/item.php`
build them and pass the request in.

Generate the autoloader, then serve `public/`:

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Open http://localhost:8000, then try:

```
curl 'http://localhost:8000/?category=drinks'
curl -i 'http://localhost:8000/item.php?id=99'
```
