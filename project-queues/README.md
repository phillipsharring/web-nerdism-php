# Queues

Project: https://phillipharrington.com/php/projects/queues

A signup form that doesn't send its welcome email itself: it adds a job to a
queue (a MySQL table), and a worker sends the email. Mail goes to Mailpit,
which catches it and shows it at http://localhost:8025. Install, then start
everything:

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker compose up
```

Sign up at http://localhost:8000, and the email appears in Mailpit a moment
later. Run a worker by hand until the queue is empty:

```
docker compose run --rm app php bin/worker.php --until-empty
```

`Ctrl+C` stops it; `docker compose down -v` removes the database. (`APP_PORT`
and `MAIL_PORT` change the ports.)
