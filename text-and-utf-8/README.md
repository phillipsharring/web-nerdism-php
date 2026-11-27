# Text and UTF-8

Lesson: https://phillipharrington.com/php/text-and-utf-8

Bytes and characters, on the command line:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php bytes.php
```

Then a page that counts a name and refuses broken text:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

Open http://localhost:8000 and type a name with an accent. Then send bytes
that aren't UTF-8:

```
curl 'http://localhost:8000/?name=caf%C3%A9'
curl 'http://localhost:8000/?name=caf%E9'
```
