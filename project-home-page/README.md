# Project: Home Page

Project: https://phillipharrington.com/php/projects/home-page

A home page with a little PHP in it: a greeting by the time of day, a list of
links from an array, and a footer year that never goes stale.

Start the server, serving only `public/`:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Open http://localhost:8000. Then look at what the browser got:

```
curl http://localhost:8000
```
