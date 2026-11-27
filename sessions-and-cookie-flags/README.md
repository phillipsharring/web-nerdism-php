# Sessions and Cookie Flags

Lesson: https://phillipharrington.com/php/sessions-and-cookie-flags

Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

The pretend member is alice@example.com, password `correct horse battery staple`.
Compare the cookies:

```
curl -I http://localhost:8000/naive.php
curl -I http://localhost:8000/
```

Session fixation, against the naive page: the attacker picks an id, the victim
logs in with it, and the attacker is in.

```
curl -b 'PHPSESSID=attacker-picked-1' --data-urlencode 'email=alice@example.com' \
    --data-urlencode 'password=correct horse battery staple' http://localhost:8000/naive.php
curl -b 'PHPSESSID=attacker-picked-1' http://localhost:8000/naive.php
```

Try the same two against `http://localhost:8000/` (the fixed version), with a
new id, and the attacker isn't logged in.
