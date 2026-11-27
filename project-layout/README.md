# Project: Layout

Project: https://phillipharrington.com/php/projects/layout

One layout file wraps every page. Each page is a template with only its own
content; `render()` runs a template and returns its output, and
`public/index.php` picks the template for each path, or a 404.

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Open http://localhost:8000, then http://localhost:8000/about and
http://localhost:8000/nowhere.

```
curl -i http://localhost:8000/nowhere
```
