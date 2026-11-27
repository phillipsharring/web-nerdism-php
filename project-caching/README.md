# Caching

Project: https://phillipharrington.com/php/projects/caching

A slow report (one second, every time), cached two ways: in files on the
server for 60 seconds, and in the browser with `Cache-Control` and an `ETag`.
Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Then, from a second terminal:

```
curl -i 'http://localhost:8000/report.php?region=north'     # X-Cache: MISS, a second
curl -i 'http://localhost:8000/report.php?region=north'     # X-Cache: HIT, at once
curl -i -H 'If-None-Match: "<the ETag>"' 'http://localhost:8000/report.php?region=north'   # 304
```

Clear the cache:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php clear-cache.php
```
