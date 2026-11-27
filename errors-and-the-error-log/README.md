# Errors and the Error Log

Lesson: https://phillipharrington.com/php/errors-and-the-error-log

Development settings: errors on the page, and in the terminal.

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -d display_errors=1 -d log_errors=1
```

Production settings: nothing on the page, everything in the log.

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -d display_errors=0 -d log_errors=1
```

Either way, from a second terminal:

```
curl -i 'http://localhost:8000/?problem=warning'
curl -i 'http://localhost:8000/?problem=exception'
```
