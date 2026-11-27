# Newsletter

Project: https://phillipharrington.com/php/projects/newsletter

A newsletter that only goes to people who asked for it: double opt-in (a
confirmation link, with a button), at most one confirmation email per address
per day, five signups an hour per visitor, a honeypot, and an unsubscribe link
and one-click unsubscribe headers in every issue. Mail goes to Mailpit, at
http://localhost:8025. `public/naive.php` is the broken version from Break It;
never put it on a server. Install, then start it:

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker compose up
```

Subscribe at http://localhost:8000, confirm from the email in Mailpit, then
send an issue:

```
docker compose exec app php bin/send.php issues/2026-11.txt --dry-run
docker compose exec app php bin/send.php issues/2026-11.txt
```

`Ctrl+C` stops it; `docker compose down -v` removes the database. (`APP_PORT`
and `MAIL_PORT` change the ports.)
