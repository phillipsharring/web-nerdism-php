# Project: Sending Email

Project: https://phillipharrington.com/php/projects/sending-email

Send an email from PHP with Symfony Mailer, to Mailpit, a mail catcher that
keeps every message and shows it in a web page. Nothing real is sent.

Install the mailer, start Mailpit, then send:

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker compose up -d mailpit
docker compose run --rm php php send.php alice@example.com
```

Read it at http://localhost:8025. `docker compose down` stops Mailpit and
throws the messages away. (`MAILPIT_PORT` changes the port, if 8025 is taken.)
