# Never Trust Input

Lesson: https://phillipharrington.com/php/never-trust-input

Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

`show.php` reports everything a request brought with it. Forge some of it:

```
curl -A 'Definitely a real browser' -e 'https://example.com/trust-me' -b 'admin=yes' \
    -H 'X-Forwarded-For: 1.2.3.4' -d 'price=1' 'http://localhost:8000/show.php?id[]=7&id[]=8'
```

`naive.php` sells mugs and believes the form. Break it:

```
curl -d 'qty=2&price=1' http://localhost:8000/naive.php
curl -d 'qty=-5&price=1200' http://localhost:8000/naive.php
```

`index.php` is the fixed version. The same requests get a 422, or the real price.
