# Project: Contact Form

Project: https://phillipharrington.com/php/projects/contact-form

A contact form that emails the site's owner, caught by Mailpit so nothing real
is sent. `public/naive.php` is the naive version, a spam cannon;
`public/index.php` is the fixed one: every field checked, mail only ever to
the owner, a honeypot field, and rate limits per visitor and for the whole
site.

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker compose up
```

The form is at http://localhost:8000 (naive: http://localhost:8000/naive.php).
Mailpit is at http://localhost:8025. Fire the naive one at a stranger:

```
for i in 1 2 3 4 5; do
  curl -s -o /dev/null -d 'name=Prize&email=victim@example.com&copy=on&message=You won! https://spam.example' http://localhost:8000/naive.php
done
```

`APP_PORT` and `MAILPIT_PORT` change the ports.
