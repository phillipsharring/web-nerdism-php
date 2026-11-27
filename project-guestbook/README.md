# Project: Guestbook

Project: https://phillipharrington.com/php/projects/guestbook

A guestbook that stores entries in `storage/entries.jsonl`. Both pages read
the same entries: `public/naive.php` prints them raw, `public/index.php`
escapes everything, only links real web addresses, and sends a
Content-Security-Policy.

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Sign the naive one with a script, then open both pages:

```
curl --data-urlencode "message=<script>alert('Hacked')</script>" -d 'name=Mallory' http://localhost:8000/naive.php
```

http://localhost:8000/naive.php runs it. http://localhost:8000 shows it as
text. Delete `storage/` to start over.
