# Output

Lesson: https://phillipharrington.com/php/output

The ways to print, on the command line:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php dump.php
```

Then on the web:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

```
curl 'http://localhost:8000/?name=Alice'
curl --get --data-urlencode 'name=<b>Alice</b>' http://localhost:8000/
curl -i http://localhost:8000/too-late.php
```
