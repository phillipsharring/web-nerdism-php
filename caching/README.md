# Caching

Lesson: https://phillipharrington.com/php/caching

Serve `public/`; the cache files go in `cache/`, outside it:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Then, from a second terminal:

```
curl -i -w '%{time_total}s\n' http://localhost:8000/   # MISS, two seconds
curl -i -w '%{time_total}s\n' http://localhost:8000/   # HIT, the same page, at once
curl -i -H 'If-None-Match: "paste-the-etag-here"' http://localhost:8000/   # 304
rm cache/*.html                                         # throw the copy away
curl -i 'http://localhost:8000/hello.php?name=Ada'      # private, no-store
```
