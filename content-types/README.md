# Content Types

Lesson: https://phillipharrington.com/php/content-types

Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

Then open http://localhost:8000/ with `?as=text`, `?as=json`, `?as=bytes` or `?save=1`, and look at the labels with `curl -I "http://localhost:8000/?as=text"`.
