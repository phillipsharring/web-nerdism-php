# Expressive HTTP Response Codes, and When Not to Be

Lesson: https://phillipharrington.com/php/expressive-response-codes

A tiny JSON API, twice. `before.php` tells everyone everything. `after.php` is
specific with honest mistakes and vague with anything an attacker could learn
from. `app.php` holds the pretend data and helpers both use.

Serve one at a time (each is a router script):

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 before.php
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 after.php
```

Then ask each the same questions:

```
curl -i -d 'email=nobody@example.com&password=x' http://localhost:8000/login
curl -i -d 'email=alice@example.com&password=x' http://localhost:8000/login
curl -i -H 'Authorization: Bearer alice-token' http://localhost:8000/notes/2
curl -i -H 'Authorization: Bearer alice-token' http://localhost:8000/notes/9
curl -i -H 'Authorization: Bearer alice-token' -d 'text=Hello' http://localhost:8000/notes
curl -i http://localhost:8000/login
```
