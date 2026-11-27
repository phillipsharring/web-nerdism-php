# Project: Logging

Project: https://phillipharrington.com/php/projects/logging

A small app that writes what it does to `logs/app.log`, with Monolog: one line
per event, a request id on every line, and uncaught exceptions logged in full
while the visitor sees only an apology and the id.

Install Monolog, then start the server:

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

In a second terminal, watch the log while you make requests from a third:

```
tail -f logs/app.log
curl http://localhost:8000/
curl 'http://localhost:8000/search?q=caf%C3%A9'
curl -i http://localhost:8000/boom
```
