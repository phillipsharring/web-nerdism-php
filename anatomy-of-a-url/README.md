# Anatomy of a URL

Lesson: https://phillipharrington.com/php/anatomy-of-a-url

Take a URL apart (give it your own, in quotes, after `url.php`):

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php url.php
```

See which parts reach a server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

Then open http://localhost:8000/anything/you/like?name=you&page=2#here
